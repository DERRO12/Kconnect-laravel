    <div class="member-card">
        <div class="member-image">
            @if (!empty($member['photo']))
                <img src="../uploads/employees/{{ $member['photo'] }}" 
                     alt="{{ $member['full_name'] }}">
            @else
                <div class="member-image-placeholder">
                    {!! strtoupper(substr($member['full_name'], 0, 1)) !!}
                </div>
            @endif
        </div>
        <div class="member-info">
            <h3 class="member-name">{{ $member['full_name'] }}</h3>
            <p class="member-position">{{ $member['position'] ?? 'Committee Member' }}</p>
        </div>
    </div>