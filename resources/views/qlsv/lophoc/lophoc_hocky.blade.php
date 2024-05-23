@extends('layouts.admin-vue')

@section('content')
<lophoc-monhoc-edit-box parent_url="{{ $parentUrl }}" :lh_id="{{ $id }}"></lophoc-monhoc-edit-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
