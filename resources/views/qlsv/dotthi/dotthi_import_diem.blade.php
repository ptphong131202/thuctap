@extends('layouts.admin-vue')

@section('content')
<importdiem-box :dt_id="{{ $dt_id }}" :lh_id="{{ $lh_id }}" :lh_nienche="{{$lophoc->lh_nienche}}"
     :router="'{{ route('dot-thi.detail', $dt_id); }}'"></importdiem-box>
@endsection

@push('scripts')
<script type="text/javascript">
</script>
@endpush
