@extends('layouts.admin-vue')

@section('content')
<nhatky-chitiet-box sv_id={{ $sv_id }} sinh_vien="{{ $sinhvien }}"  permissions="{{$permissions}}" parent_url={{ $parentUrl }}></nhatky-chitiet-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
