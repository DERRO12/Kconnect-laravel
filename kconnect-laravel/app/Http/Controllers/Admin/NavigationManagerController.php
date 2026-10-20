<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Navigation menu admin (admin/manage-navigation.php). */
class NavigationManagerController extends Controller
{
    private function flash($message, $type): void
    {
        session(['message' => $message, 'message_type' => $type]);
    }

    /** admin/manage-navigation.php */
    public function index(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login.php');
        }

        // Handle AJAX reorder request
        if ($request->post('action') !== null && $request->post('action') == 'reorder') {
            $order_data = json_decode((string) $request->post('order_data'), true);

            DB::beginTransaction();

            try {
                $all_items = [];
                foreach (DB::select('SELECT id, parent_id, sort_order FROM navigation_links') as $row) {
                    $all_items[$row['id']] = $row;
                }

                foreach ((is_array($order_data) ? $order_data : []) as $item) {
                    $id = (int) $item['id'];
                    $sort_order = (int) $item['sort_order'];
                    $parent_id = ($item['parent_id'] ?? null) === null ? 'NULL' : (int) $item['parent_id'];

                    if (! isset($all_items[$id])) {
                        throw new \Exception("Item ID $id not found");
                    }

                    if ($parent_id !== 'NULL') {
                        $check_parent = $parent_id;
                        while ($check_parent !== null) {
                            if ($check_parent == $id) {
                                throw new \Exception('Cannot make an item a child of itself');
                            }
                            if (! isset($all_items[$check_parent])) {
                                throw new \Exception("Parent ID $check_parent not found");
                            }
                            $check_parent = $all_items[$check_parent]['parent_id'] ?? null;
                        }
                    }

                    DB::update("UPDATE navigation_links SET sort_order = $sort_order, parent_id = ".
                        ($parent_id === 'NULL' ? 'NULL' : $parent_id).
                        " WHERE id = $id");
                }

                DB::commit();

                return kc_json(['success' => true, 'message' => 'Order updated successfully']);
            } catch (\Exception $e) {
                DB::rollBack();

                return kc_json(['success' => false, 'message' => 'Error updating order: '.$e->getMessage()]);
            }
        }

        $action = $request->query('action') !== null ? $request->query('action') : '';
        $id = $request->query('id') !== null ? (int) $request->query('id') : 0;

        // DELETE action
        if ($action == 'delete' && $id > 0) {
            $this->deleteBranch($id);
            DB::delete("DELETE FROM navigation_links WHERE id = $id");

            $this->flash('Item deleted successfully!', 'success');
            kc_redirect('manage-navigation.php');
        }

        // TOGGLE STATUS action
        if ($action == 'toggle' && $id > 0) {
            $current = DB::selectOne("SELECT status FROM navigation_links WHERE id = $id");
            if ($current) {
                $newStatus = $current['status'] ? 0 : 1;
                DB::update("UPDATE navigation_links SET status = $newStatus WHERE id = $id");
                $this->flash('Status updated successfully!', 'success');
            }
            kc_redirect('manage-navigation.php');
        }

        // Handle Add/Edit form submission
        if ($request->isMethod('post') && $request->post('action') === null) {
            $title = $request->post('title') !== null ? trim($request->post('title')) : '';
            $url = $request->post('url') !== null ? trim($request->post('url')) : '';
            $parent_id = $request->post('parent_id') !== null && $request->post('parent_id') !== '' ? (int) $request->post('parent_id') : 'NULL';
            $is_label = $request->post('is_label') !== null ? 1 : 0;
            $status = $request->post('status') !== null ? 1 : 0;
            $edit_id = $request->post('edit_id') !== null ? (int) $request->post('edit_id') : 0;

            if (empty($title)) {
                $this->flash('Title is required!', 'danger');
                kc_redirect('manage-navigation.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            if ($is_label) {
                $url = '';
            }

            $url_value = ! empty($url) ? $url : null;
            $parent_value = $parent_id === 'NULL' ? 'NULL' : $parent_id;

            if ($edit_id > 0) {
                try {
                    DB::update("UPDATE navigation_links SET 
                title = ?,
                url = ?,
                parent_id = $parent_value,
                is_label = $is_label,
                status = $status
                WHERE id = $edit_id", [$title, $url_value]);
                    $this->flash('Item updated successfully!', 'success');
                } catch (\Illuminate\Database\QueryException $e) {
                    $this->flash('Error: '.$e->getMessage(), 'danger');
                }
            } else {
                if ($parent_id === 'NULL') {
                    $max_order_sql = 'SELECT MAX(sort_order) as max FROM navigation_links WHERE parent_id IS NULL';
                } else {
                    $max_order_sql = "SELECT MAX(sort_order) as max FROM navigation_links WHERE parent_id = $parent_id";
                }
                $max_order = DB::selectOne($max_order_sql)['max'];
                $sort_order = ($max_order !== null) ? $max_order + 1 : 0;

                try {
                    DB::insert("INSERT INTO navigation_links 
                (title, url, parent_id, is_label, sort_order, status) 
                VALUES (
                    ?,
                    ?,
                    $parent_value,
                    $is_label,
                    $sort_order,
                    $status
                )", [$title, $url_value]);
                    $this->flash('Item added successfully!', 'success');
                } catch (\Illuminate\Database\QueryException $e) {
                    $this->flash('Error: '.$e->getMessage(), 'danger');
                }
            }

            kc_redirect('manage-navigation.php');
        }

        $edit_item = null;
        if ($request->query('edit') !== null) {
            $edit_id = (int) $request->query('edit');
            $edit_item = DB::selectOne("SELECT * FROM navigation_links WHERE id = $edit_id");
        }

        $items_array = DB::select('SELECT * FROM navigation_links ORDER BY 
    CASE WHEN parent_id IS NULL THEN id ELSE parent_id END, 
    sort_order ASC, 
    id ASC');

        $tree = $this->buildTree($items_array);

        $total = DB::selectOne('SELECT COUNT(*) as c FROM navigation_links')['c'];
        $active = DB::selectOne('SELECT COUNT(*) as c FROM navigation_links WHERE status = 1')['c'];
        $labels = DB::selectOne('SELECT COUNT(*) as c FROM navigation_links WHERE is_label = 1')['c'];
        $links = $total - $labels;

        $parent_options_html = $this->getParentOptions();

        $message = session('message');
        $message_type = session('message_type');
        session()->forget(['message', 'message_type']);

        return view('admin.manage-navigation', compact(
            'edit_item', 'tree', 'total', 'active', 'labels', 'links', 'parent_options_html', 'message', 'message_type'
        ));
    }

    /** Old deleteBranch($parent_id) */
    private function deleteBranch($parent_id): void
    {
        foreach (DB::select("SELECT id FROM navigation_links WHERE parent_id = $parent_id") as $child) {
            $this->deleteBranch($child['id']);
            DB::delete("DELETE FROM navigation_links WHERE id = {$child['id']}");
        }
    }

    /** Old buildTree($items, $parent_id = null) */
    private function buildTree($items, $parent_id = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            $item_parent = $item['parent_id'] === null ? null : $item['parent_id'];
            if ($item_parent == $parent_id) {
                $children = $this->buildTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        usort($tree, function ($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        return $tree;
    }

    /** Old getParentOptions() (echoed the <option>s; now returns them) */
    private function getParentOptions($parent_id = null, $level = 0, $exclude_id = null): string
    {
        $out = '';
        $sql = $parent_id === null
            ? 'SELECT * FROM navigation_links WHERE parent_id IS NULL ORDER BY sort_order'
            : "SELECT * FROM navigation_links WHERE parent_id = $parent_id ORDER BY sort_order";
        foreach (DB::select($sql) as $row) {
            if ($exclude_id && $row['id'] == $exclude_id) {
                continue;
            }
            $indent = str_repeat('—', $level).' ';
            $out .= '<option value="'.$row['id'].'">'.$indent.htmlspecialchars($row['title']).'</option>';
            $out .= $this->getParentOptions($row['id'], $level + 1, $exclude_id);
        }

        return $out;
    }
}
