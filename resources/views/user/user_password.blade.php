@extends('layouts.admin-vue')

@section('content')
<user-password-box :userid="{{ $userid }}"></user-password-box>
@endsection

@push('scripts')
<script type="text/javascript">
   
</script>
@endpush