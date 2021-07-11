@if($value == App\Library\Auditing\FullRedact::REDACTION_TOKEN)
    <span class="badge bg-dark">Redacted</span>
@elseif($value === true)
    <i class="fas fa-check text-success"></i>
@elseif($value === false)
    <i class="fas fa-times text-danger"></i>
@elseif(is_null($value))
    <span class="badge bg-secondary">NULL</span>
@elseif($value == "")
    <span class="badge bg-secondary">Empty</span>
@else
    {{ $value }}
@endif
