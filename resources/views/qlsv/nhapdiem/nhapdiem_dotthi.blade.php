@extends('layouts.admin-vue')

@section('content')
<nhapdiem-dotthi-box :lh_id="'{{ $lopHoc->lh_id }}'"
    :dot_thi="'{{ $dotthi->dt_ten }}'"
    :dt_id="{{ $dotthi->dt_id }}"
    :mh_id="'{{ $monHoc->mh_id }}'"></nhapdiem-dotthi-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
