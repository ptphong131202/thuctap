@extends('layouts.admin-vue')

@section('content')
<nhapdiem-hocky-box parent_url="{{$parentUrl}}" :lh_id="'{{ $lopHoc->lh_id }}'" permissions="{{$permissions}}"></nhapdiem-hocky-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
