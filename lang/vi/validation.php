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

    'accepted'                => 'Mục :attribute phải được chấp nhận.',
    'active_url'              => 'Mục :attribute không phải là một URL hợp lệ.',
    'after'                   => 'Mục :attribute phải là một ngày sau ngày :date.',
    'after_or_equal'          => 'Mục :attribute phải là thời gian bắt đầu sau hoặc đúng bằng :date.',
    'alpha'                   => 'Mục :attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash'              => 'Mục :attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num'               => 'Mục :attribute chỉ có thể chứa chữ cái và số.',
    'array'                   => 'Mục :attribute phải là dạng mảng.',
    'before'                  => 'Mục :attribute phải là một ngày trước ngày :date.',
    'before_or_equal'         => 'Mục :attribute phải là thời gian bắt đầu trước hoặc đúng bằng :date.',
    'between'                 => [
        'numeric' => 'Mục :attribute phải nằm trong khoảng :min - :max.',
        'file'    => 'Dung lượng tập tin trong mục :attribute phải từ :min - :max kB.',
        'string'  => 'Mục :attribute phải từ :min - :max ký tự.',
        'array'   => 'Mục :attribute phải có từ :min - :max phần tử.',
    ],
    'boolean'                 => 'Mục :attribute phải là true hoặc false.',
    'confirmed'               => 'Giá trị xác nhận trong mục :attribute không khớp.',
    'date'                    => 'Mục :attribute không phải là định dạng của ngày-tháng.',
    'date_equals'             => 'Mục :attribute phải là một ngày bằng với :date.',
    'date_format'             => 'Mục :attribute không giống với định dạng :format.',
    'different'               => 'Mục :attribute và :other phải khác nhau.',
    'digits'                  => 'Độ dài của mục :attribute phải gồm :digits chữ số.',
    'digits_between'          => 'Độ dài của mục :attribute phải nằm trong khoảng :min and :max chữ số.',
    'dimensions'              => 'Mục :attribute có kích thước không hợp lệ.',
    'distinct'                => 'Mục :attribute có giá trị trùng lặp.',
    'email'                   => 'Mục :attribute phải là một địa chỉ email hợp lệ.',
    'ends_with'               => 'Mục :attribute phải kết thúc bằng một trong những giá trị sau: :values',
    'exists'                  => 'Giá trị đã chọn trong mục :attribute không hợp lệ.',
    'file'                    => 'Mục :attribute phải là một tệp tin.',
    'filled'                  => 'Mục :attribute không được bỏ trống.',
    'gt'                      => [
        'numeric' => 'Giá trị mục :attribute phải lớn hơn :value.',
        'file'    => 'Dung lượng mục :attribute phải lớn hơn :value kilobytes.',
        'string'  => 'Độ dài mục :attribute phải nhiều hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có nhiều hơn :value phần tử.',
        'date'    => "Mục :attribute phải là một ngày lớn hơn :date",
    ],
    'gte'                     => [
        'numeric' => 'Giá trị mục :attribute phải lớn hơn hoặc bằng :value.',
        'file'    => 'Dung lượng mục :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài mục :attribute phải lớn hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít nhất :value phần tử.',
    ],
    'image'                   => 'Mục :attribute phải là định dạng hình ảnh.',
    'in'                      => 'Giá trị đã chọn trong mục :attribute không hợp lệ.',
    'in_array'                => 'Mục :attribute phải thuộc tập cho phép: :other.',
    'integer'                 => 'Mục :attribute phải là một số nguyên.',
    'ip'                      => 'Mục :attribute phải là một địa chỉ IP.',
    'ipv4'                    => 'Mục :attribute phải là một địa chỉ IPv4.',
    'ipv6'                    => 'Mục :attribute phải là một địa chỉ IPv6.',
    'json'                    => 'Mục :attribute phải là một chuỗi JSON.',
    'lt'                      => [
        'numeric' => 'Giá trị mục :attribute phải nhỏ hơn :value.',
        'file'    => 'Dung lượng mục :attribute phải nhỏ hơn :value kilobytes.',
        'string'  => 'Độ dài mục :attribute phải nhỏ hơn :value kí tự.',
        'array'   => 'Mảng :attribute phải có ít hơn :value phần tử.',
    ],
    'lte'                     => [
        'numeric' => 'Giá trị mục :attribute phải nhỏ hơn hoặc bằng :value.',
        'file'    => 'Dung lượng mục :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string'  => 'Độ dài mục :attribute phải nhỏ hơn hoặc bằng :value kí tự.',
        'array'   => 'Mảng :attribute không được có nhiều hơn :value phần tử.',
        'date'    => "Mục :attribute phải là một ngày nhỏ hơn hoặc bẳng :date",
    ],
    'max'                     => [
        'numeric' => 'Mục :attribute không được lớn hơn :max.',
        'file'    => 'Dung lượng tập tin trong mục :attribute không được lớn hơn :max kB.',
        'string'  => 'Mục :attribute không được lớn hơn :max ký tự.',
        'array'   => 'Mục :attribute không được lớn hơn :max phần tử.',
    ],
    'mimes'                   => 'Mục :attribute phải là một tập tin có định dạng: :values.',
    'mimetypes'               => 'Mục :attribute phải là một tập tin có định dạng: :values.',
    'file_extension'          => 'Mục :attribute phải là một tập tin có định dạng: :values.',
    'min'                     => [
        'numeric' => 'Mục :attribute phải tối thiểu là :min.',
        'file'    => 'Dung lượng tập tin trong mục :attribute phải tối thiểu :min kB.',
        'string'  => 'Mục :attribute phải có tối thiểu :min ký tự.',
        'array'   => 'Mục :attribute phải có tối thiểu :min phần tử.',
    ],
    'multiple_of'             => 'Mục :attribute phải là bội số của :value',
    'not_in'                  => 'Giá trị đã chọn trong mục :attribute không hợp lệ.',
    'not_regex'               => 'Mục :attribute có định dạng không hợp lệ.',
    'numeric'                 => 'Mục :attribute phải là một số.',
    'password'                => 'Mật khẩu không đúng.',
    'present'                 => 'Mục :attribute phải được cung cấp.',
    'regex'                   => 'Mục :attribute có định dạng không hợp lệ.',
    'required'                => 'Mục :attribute không được bỏ trống.',
    'required_if'             => 'Mục :attribute không được bỏ trống khi mục :other là :value.',
    'required_unless'         => 'Mục :attribute không được bỏ trống trừ khi :other là :values.',
    'required_with'           => 'Mục :attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all'       => 'Mục :attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without'        => 'Mục :attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all'    => 'Mục :attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same'                    => 'Mục :attribute và :other phải giống nhau.',
    'size'                    => [
        'numeric' => 'Mục :attribute phải bằng :size.',
        'file'    => 'Dung lượng tập tin trong mục :attribute phải bằng :size kB.',
        'string'  => 'Mục :attribute phải chứa :size ký tự.',
        'array'   => 'Mục :attribute phải chứa :size phần tử.',
    ],
    'starts_with'             => 'Mục :attribute phải được bắt đầu bằng một trong những giá trị sau: :values',
    'string'                  => 'Mục :attribute phải là một chuỗi ký tự.',
    'timezone'                => 'Mục :attribute phải là một múi giờ hợp lệ.',
    'unique'                  => 'Mục :attribute đã có trong cơ sở dữ liệu.',
    'uploaded'                => 'Mục :attribute tải lên thất bại.',
    'url'                     => 'Mục :attribute không giống với định dạng một URL.',
    'uuid'                    => 'Mục :attribute phải là một chuỗi UUID hợp lệ.',
    'percent'                 => 'Tỉ lệ phần trăm trúng thưởng phải là 100%',
    'file_extension'          => 'Mục :attribute phải là một tập tin có định dạng: :values.',
    'phone_format'            => 'Số điện thoại không hợp lệ',
    'same_password'           => 'Mật khẩu mới và Mật khẩu xác nhận chưa trùng khớp.',


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

    'attributes' => [
        'current_password'      => 'Mật khẩu cũ',
        'password'              => 'Mật khẩu',
        'password_confirmation' => 'Mật khẩu xác nhận',
    ],
];
