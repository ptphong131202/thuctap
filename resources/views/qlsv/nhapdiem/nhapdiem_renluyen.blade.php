@extends('layouts.admin-vue')

@section('content')
<nhapdiem-renluyen-box :lh_id="'{{ $lopHoc->lh_id }}'"
    :hoc_ky="'{{ $hocKy }}'"></nhapdiem-renluyen-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush