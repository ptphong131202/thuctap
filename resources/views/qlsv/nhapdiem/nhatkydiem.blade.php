@extends('layouts.admin-vue')

@section('content')
<nhat-ky-sua-diem-box :bd_id="'{{ $bd_id }}'" :lh_id="'{{ $lh_id }}'" :mh_id="'{{ $mh_id }}'"
:thoigian="'{{ $thoigian }}'"></nhat-ky-sua-diem-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush