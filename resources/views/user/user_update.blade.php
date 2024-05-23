@extends('layouts.admin-vue')

@section('content')
@isset($userid)
<user-edit-box :userid="{{ $userid }}"></user-edit-box>
@else
<user-edit-box></user-edit-box>
@endisset
@endsection

@push('scripts')
<script type="text/javascript">
   
</script>
@endpush