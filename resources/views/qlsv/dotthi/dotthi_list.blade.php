@extends('layouts.admin-vue')

@section('content')
<dotthi-box permissions="{{$permissions}}"></dotthi-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
