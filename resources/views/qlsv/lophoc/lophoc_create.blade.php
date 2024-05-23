@extends('layouts.admin-vue')

@section('content')
<lophoc-edit-box parent_url="{{ $parentUrl }}" :lh_id="{{ $id }}" :router="'{{ route('lop-hoc.them-sinh-vien-excel', $id); }}'"></lophoc-edit-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
