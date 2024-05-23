@extends('layouts.admin')

@section('header')
    <h1>
        Nhật ký lớp {{ $lopHoc->lh_ma }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li  class="active"><a href="{{ $parentUrl }}">Nhật ký</a></li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <a href="{{ $parentUrl }}" style="margin-bottom:6px;" title="Trở về danh sách" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
            <div class="box box-widget">
                <div class="box-body">
                    @if (!$danhSachNamHoc->isEmpty() && !$danhSachSinhVien->isEmpty())
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered no-margin " id="table-sinhvien">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="w-3 text-center">#</th>
                                        <th rowspan="2" class="w-10 text-center">MSHS/MSSV</th>
                                        <th>Họ và tên</th>
                                        <th>Lịch sử đăng nhập gần nhất</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($danhSachSinhVien as $sinhVien)
                                        <tr class="text-middle no-wrap">
                                            <td class="text-center">{{ $loop->index + 1 }}</td>
                                            <td class="text-center">{{ $sinhVien->sv_ma }}</td>
                                            <td>{{ $sinhVien->sv_ho . " ". $sinhVien->sv_ten  }}</td>
                                            <td>{{$sinhVien->svLog != null ? date("d/m/Y h:m:s", strtotime( $sinhVien->svLog->created_at)) : "Chưa đăng nhập" }}</td>
                                            {{-- <td><a href={{ "/nhat-ky/" . $sinhVien->sv_id . "/chi-tiet" }} class="btn bg-purple btn-sm" title="Chi tiết đợt thi"><i class="fa fa-eye"> Xem chi tiết nhật ký</i></td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="clearfix"></div>
                        <div class="col-lg-12 text-center">
                            Không tìm thấy dữ liệu
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ $parentUrl }}" title="Trở về danh sách" class="btn btn-default">
                <i class="fa fa-share"></i> Trở về danh sách
            </a>
        </div>
    </div>

    <div class="modal fade" id="modal-chondotthi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Thêm đợt thi</h4>
                </div>
                <div class="modal-body">
                    <select class="form-control select2" id="slDotThi" name="slDotThi">
                        @foreach ($danhSachDotThi as $dotthi)
                            <option value="{{ $dotthi->dt_id }}">{{ $dotthi->dt_ten }} -
                                {{ $dotthi->dt_tuthang . '/' . $dotthi->dt_tunam }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="themDotThi();" class="btn btn-danger">Lưu</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var lhId = {{ $lhId }};

        $('.select2').select2({
            width: '100%',
            language: {
                noResults: function() {
                    return "Không tìm thấy kết quả";
                }
            },
            placeholder: 'Nhập ký tự để tìm kiếm',
        });

        function redirect() {
            window.location = '?semester=' + $('#selectKqht').val() + '&sorttotnghiep=' + ($('#sortTotNghiep').val() ? $(
                '#sortTotNghiep').val() : false);
        }

        function timKiem() {
            var key = $('#txtThongTin').val();
            var row = $('#table-sinhvien tbody tr');
            row.css('display', 'none');
            row.each(function() {
                text = $(this).find('td:nth-child(4)').text() + $(this).find('td:nth-child(5)').text() + $(this)
                    .find('td:nth-child(6)').text();
                if (text.toLowerCase().includes(key.toLowerCase())) {
                    $(this).css('display', '');
                }
            });
        }

        function checkXuatWord() {
            $('.sosinhvien').text($("input[name='sinhvien[]']:checked").length);
        }

        function checkAllXuatWord() {
            if ($('#checkall').is(":checked")) {
                $("input[name='sinhvien[]']").prop('checked', true);
            } else {
                $("input[name='sinhvien[]']").prop('checked', false);
            }
            checkXuatWord();
        }

        function xuatWord() {
            arrSinhVien = $("input[name='sinhvien[]']:checked").map(function() {
                return $(this).val();
            }).get();
            if (arrSinhVien.length > 0) {
                $.each(arrSinhVien, function(index, value) {
                    var win = window.open("{{ route('tra-chu-xuat-ket-qua') }}?sv_id=" + value +
                        "&lhId="+lhId+"&hoc_ky={{ request()->semester }}", '_blank');
                    if (win) {
                        win.focus();
                    } else {
                        alert('Vui lòng cho phép trang web mở thêm nhiều cửa sổ');
                        return false;
                    }
                });
            } else {
                alert('Vui lòng chọn sinh viên muốn xuất thông tin');
            }
        }

        function themDotThi() {
            var sv = $("input[name='sinhvien[]']:checked").map(function() {
                return $(this).val();
            }).get();
            var json_sv = [];
            sv.forEach(function(e) {
                json_sv.push(JSON.parse($('#chbSV' + e).text()));
            });
            $.ajax({
                type: 'POST',
                url: "{{ route('themdotthi') }}",
                data: {
                    'dotThi': $('#slDotThi').val(),
                    'lopHoc': {{ $lopHoc->lh_id }},
                    '_token': "{{ csrf_token() }}",
                    'sinhVien': json_sv,
                },
                success: function(data) {
                    alert('Thêm đợt thi thành công');
                    $('#modal-chondotthi').modal('hide');
                }
            });
        }
    </script>
@endpush
