@props(['status', 'class' => ''])

<span class="badge {{ $status->badgeClass() }} {{ $class }}">{{ $status->label() }}</span>