<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table `whistleblowing_reports` (existing K-Connect table, schema unchanged).
 *
 * @property string $id  int(11)
 * @property string $reference_number  varchar(50)
 * @property string $submission_type  enum('anonymous','confidential','open')
 * @property string|null $full_name  varchar(255)
 * @property string|null $entity  varchar(100)
 * @property string|null $department  varchar(255)
 * @property string|null $contact_method  varchar(255)
 * @property string $relationship  enum('current_employee','former_employee','contractor','supplier','customer','other')
 * @property string|null $relationship_other  varchar(255)
 * @property string|null $nature_concerns  text
 * @property string|null $concern_other  varchar(255)
 * @property string $what_happened  text
 * @property string|null $who_involved  text
 * @property string|null $incident_type  enum('single','recurring')
 * @property string|null $incident_date  date
 * @property string|null $incident_from_date  date
 * @property string|null $incident_to_date  date
 * @property string|null $incident_location  text
 * @property string|null $awareness_method  varchar(255)
 * @property string|null $awareness_other  varchar(255)
 * @property string|null $estimated_impact  varchar(255)
 * @property string|null $evidence_description  text
 * @property string|null $witnesses  text
 * @property string|null $prior_reported  tinyint(1)
 * @property string|null $prior_report_details  text
 * @property string|null $urgent  tinyint(1)
 * @property string|null $involves_handler  tinyint(1)
 * @property string|null $declaration_signature  varchar(255)
 * @property string|null $declaration_date  date
 * @property string $status  enum('new','under_review','investigating','resolved','closed','dismissed')
 * @property string|null $admin_notes  text
 * @property string|null $resolution_notes  text
 * @property string|null $received_date  datetime
 * @property string $created_at  timestamp
 * @property string $updated_at  timestamp
 */
class WhistleblowingReport extends Model
{
    protected $table = 'whistleblowing_reports';

    // created_at / updated_at are filled by the database defaults, as before
    public $timestamps = false;

    protected $guarded = [];
}
