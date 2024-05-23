@extends('layouts.admin-vue')

@section('content')
<import-monhoc-box router="{{ route('mon-hoc.index'); }}" permissions="{{$permissions}}"></import-monhoc-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush