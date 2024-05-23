<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => ':attribute không phải Url',
    'after' => ':attribute phải sau ngày :date.',
    'after_or_equal' => ':attribute phải sau hoặc bằng :date.',
    'alpha' => ':attribute chỉ đuợc nhập ký tự',
    'alpha_dash' => ':attribute chỉ được nhập ký tự, số, dấu - hoặc _.',
    'alpha_num' => ':attribute chỉ được nhập ký tự hoặc số',
    'array' => ':attribute phải là mảng.',
    'before' => ':attribute phải trước ngày :date.',
    'before_or_equal' => ':attribute phải trước hoặc bằng ngày :date.',
    'between' => [
        'numeric' => ':attribute từ :min đến :max.',
        'file' => ':attribute phải có dung lượng từ :min đến :max kilobytes.',
        'string' => ':attribute phải có ít nhất :min và nhiều nhất :max ký tự.',
        'array' => ':attribute phải có ít nhất :min và nhiều nhất :max phần tử.',
    ],
    'boolean' => ':attribute phải có giá trị đúng hoặc sai.',
    'confirmed' => 'Xác nhận :attribute chưa đúng',
    'date' => ':attribute sai định dạng ngày.',
    'date_equals' => ':attribute phải là ngày :date.',
    'date_format' => ':attribute chưa đúng định dạng :format.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải có :digits chữ số.',
    'digits_between' => ':attribute có từ :min đến :max chữ số.',
    'dimensions' => ':attribute có kích thước không hợp lệ.',
    'distinct' => ':attribute trùng giá trị.',
    'email' => ':attribute chưa đúng định dạng',
    'ends_with' => ':attribute phải kết thúc bằng: :values',
    'exists' => 'Lựa chọn :attribute đã tồn tại.',
    'file' => ':attribute phải là tập tin.',
    'filled' => ':attribute không được bỏ trống.',
    'gt' => [
        'numeric' => ':attribute phải lớn hơn :value.',
        'file' => ':attribute phải lớn hơn :value kilobytes.',
        'string' => ':attribute phải có nhiều hơn hơn :value ký tự.',
        'array' => ':attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'file' => ':attribute phải lơn hơn hoặc bằng :value kilobytes.',
        'string' => ':attribute phải có :value ký tự hoặc nhiều hơn.',
        'array' => ':attribute phải có :value phần tử hoặc nhiều hơn.',
    ],
    'image' => ':attribute phải là hình ảnh.',
    'in' => 'Lựa chọn :attribute không hợp lệ.',
    'in_array' => ':attribute không có trong :other.',
    'integer' => ':attribute phải là số.',
    'ip' => ':attribute phải là địa chỉ IP.',
    'ipv4' => ':attribute phải là địa chỉ IPv4.',
    'ipv6' => ':attribute phải là địa chỉ IPv6.',
    'json' => ':attribute phải là JSON.',
    'lt' => [
        'numeric' => ':attribute phải bé hơn :value.',
        'file' => ':attribute phải bé hơn :value kilobytes.',
        'string' => ':attribute phải có ít hơn :value ký tự.',
        'array' => ':attribute phải có ít hơn :value phần tử.',
    ],
    'lte' => [
        'numeric' => ':attribute phải từ :value hoặc bé hơn.',
        'file' => ':attribute phải có dung lượng :value kilobytes hoặc bé hơn.',
        'string' => ':attribute phải có :value ký tự hoặc ít hơn.',
        'array' => ':attribute phải có :value items hoặc ít hơn.',
    ],
    'max' => [
        'numeric' => ':attribute tối đa :max ký tự',
        'file' => ':attribute tối đa :max kilobytes.',
        'string' => ':attribute tối đa :max ký tự.',
        'array' => ':attribute tối đa :max phần tử.',
    ],
    'mimes' => ':attribute phải thuộc định dạng: :values.',
    'mimetypes' => ':attribute phải thuộc định dạng: :values.',
    'min' => [
        'numeric' => ':attribute phải ít nhất :min ký tự',
        'file' => ':attribute phải có ít nhất :min kilobytes.',
        'string' => ':attribute phải có ít nhất :min ký tự.',
        'array' => ':attribute phải có ít nhất :min phần tử.',
    ],
    'not_in' => 'Lựa chọn :attribute không hợp lệ.',
    'not_regex' => ':attribute định dạng không hợp lệ.',
    'numeric' => ':attribute phải là số.',
    'present' => 'Cần có dữ liệu :attribute.',
    'regex' => ':attribute không hợp lệ.',
    'required' => 'Vui lòng nhập :attribute',
    'required_if' => ':attribute không được bỏ trống nếu :other là :value.',
    'required_unless' => ':attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => ':attribute phải là :size.',
        'file' => ':attribute phải bằng :size kilobytes.',
        'string' => ':attributephải có :size ký tự.',
        'array' => ':attribute phải có :size phần tử.',
    ],
    'starts_with' => ':attribute phải bắt đầu bằng: :values',
    'string' => ':attribute phải là chuỗi ký tự',
    'timezone' => ':attribute không hợp lệ.',
    'unique' => ':attribute đã tồn tại',
    'uploaded' => ':attribute không thể tải lên.',
    'url' => ':attribute không hợp lệ.',
    'uuid' => ':attribute phải có định dạng của UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'data.*.svd_first' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_second' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_third' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_exam_first' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_exam_second' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_exam_third' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
        ],
        'data.*.svd_final' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
            'integer' => 'Điểm phải là số nguyên'
        ],
        'data.*.svd_dulop' => [
            'lte' => 'Điểm phải từ :value hoặc bé hơn',
            'gte' => 'Điểm phải từ :value hoặc lớn hơn',
            'integer' => 'Điểm phải là số nguyên'
        ],
        'data.*.sv_ma' => [
            'regex' => 'Mã sinh viên không hợp lệ',
            'required' => 'Mã sinh viên không được bõ trống',
            'unique' => 'Mã sinh viên đã tồn tại',
            'max' => 'Mã sinh viên không được vượt quá :value ',
            'min' => 'Mã sinh viên phãi có ít nhất :value ',
        ],
        'data.*.sv_ho' => [
            'required' => 'Họ sinh viên không được bõ trống',
            'max' => 'Họ sinh viên không được vượt quá :value ',
        ],
        'data.*.sv_ten' => [
            'required' => 'Tên sinh viên không được bõ trống',
            'max' => 'Tên sinh viên không được vượt quá :value ',
        ],
        'data.*.sv_ngaysinh' => [
            'required' => 'Ngày sinh không được bõ trống',
            'date_format' => 'Ngày sinh không đúng định dạng ngày/tháng/năm',
        ],
        'data.*.mh_ma' => [
            'regex' => 'Mã môn học không hợp lệ',
            'required' => 'Mã môn học không được bõ trống',
            'unique' => 'Mã môn học đã tồn tại',
            'max' => 'Mã môn học không được vượt quá :value ',
            'min' => 'Mã môn học phãi có ít nhất :value ',
        ],
        'data.*.mh_ten' => [
            'required' => 'Tên môn học không được bõ trống',
            'max' => 'Tên môn học không được vượt quá :value ',
        ],
        'data.*.mh_sodonvihoctrinh' => [
            'required' => 'Số tính chỉ không được bõ trống',
            'max' => 'Số tính chỉ không được vượt quá :value ',
            'integer' => 'Số tính chỉ không đúng định dạng ',
        ],
        'data.*.mh_sotiet' => [
            'required' => 'Số tiết/giờ không được bõ trống',
            'max' => 'Số tiết/giờ không được vượt quá :value ',
            'integer' => 'Số tiết/giờ không đúng định dạng ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
