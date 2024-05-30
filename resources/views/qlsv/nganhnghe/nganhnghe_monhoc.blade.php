<!-- P.Dinh -->
@extends('layouts.admin-vue')

@section('content')
<nganhnghe-monhoc-box excel="{{ route('mon-hoc.import-excel') }}" parent_url={{ $parentUrl }} :nn_id="{{ $nn_id }}"  :hdt_id="{{ $hdt_id }}"></nganhnghe-monhoc-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
