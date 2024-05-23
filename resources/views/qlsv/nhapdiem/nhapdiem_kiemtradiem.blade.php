@extends('layouts.admin-vue')

@section('content')
<kiem-tra-diem-box :confighssv="{{ $configSv }}"></kiem-tra-diem-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
