@extends('layouts.admin-vue')

@section('content')
<nhapdiem-monhoc-box :lh_id="'{{ $lopHoc->lh_id }}'"
    :hoc_ky="'{{ $hocKy }}'"
    :mh_id="'{{ $monHoc->mh_id }}'"></nhapdiem-monhoc-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush