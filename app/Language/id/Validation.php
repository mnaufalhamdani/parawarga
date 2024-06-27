<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// Validation language settings
return [
    // Core Messages
    'noRuleSets'      => 'Tidak ada aturan yang ditentukan dalam konfigurasi Validasi.',
    'ruleNotFound'    => '"{0}" bukan sebuah aturan yang valid.',
    'groupNotFound'   => '"{0}" bukan sebuah grup aturan validasi.',
    'groupNotArray'   => '"{0}" grup aturan harus berupa sebuah array.',
    'invalidTemplate' => '"{0}" bukan sebuah template Validasi yang valid.',

    // Rule Messages
    'alpha'                 => 'Isian {field} hanya boleh mengandung karakter alfabet.',
    'alpha_dash'            => 'Isian {field} hanya boleh berisi karakter alfanumerik, setrip bawah, dan tanda pisah.',
    'alpha_numeric'         => 'Isian {field} hanya boleh berisi karakter alfanumerik.',
    'alpha_numeric_punct'   => 'Isian {field} hanya boleh berisi karakter alfanumerik, spasi, dan karakter ~! # $% & * - _ + = | :..',
    'alpha_numeric_space'   => 'Isian {field} hanya boleh berisi karakter alfanumerik dan spasi.',
    'alpha_space'           => 'Isian {field} hanya boleh berisi karakter alfabet dan spasi.',
    'decimal'               => 'Isian {field} harus mengandung sebuah angka desimal.',
    'differs'               => 'Isian {field} harus berbeda dari isian {param}.',
    'equals'                => 'Isian {field} harus persis: {param}.',
    'exact_length'          => 'Isian {field} harus tepat {param} panjang karakter.',
    'greater_than'          => 'Isian {field} harus berisi sebuah angka yang lebih besar dari {param}.',
    'greater_than_equal_to' => 'Isian {field} harus berisi sebuah angka yang lebih besar atau sama dengan {param}.',
    'hex'                   => 'Isian {field} hanya boleh berisi karakter heksadesimal.',
    'in_list'               => 'Isian {field} harus salah satu dari: {param}.',
    'integer'               => 'Isian {field} harus mengandung bilangan bulat.',
    'is_natural'            => 'Isian {field} hanya boleh berisi angka.',
    'is_natural_no_zero'    => 'Isian {field} hanya boleh berisi angka dan harus lebih besar dari nol.',
    'is_not_unique'         => 'Isian {field} harus berisi nilai yang sudah ada sebelumnya dalam database.',
    'is_unique'             => 'Isian {field} harus mengandung sebuah nilai unik.',
    'less_than'             => 'Isian {field} harus berisi sebuah angka yang kurang dari {param}.',
    'less_than_equal_to'    => 'Isian {field} harus berisi sebuah angka yang kurang dari atau sama dengan {param}.',
    'matches'               => 'Isian {field} tidak cocok dengan isian {param}.',
    'max_length'            => 'Isian {field} tidak bisa melebihi {param} panjang karakter.',
    'min_length'            => 'Isian {field} setidaknya harus {param} panjang karakter.',
    'not_equals'            => 'Isian {field} tidak boleh: {param}.',
    'not_in_list'           => 'Isian {field} tidak boleh salah satu dari: {param}.',
    'numeric'               => 'Isian {field} hanya boleh mengandung angka.',
    'regex_match'           => 'Isian {field} tidak dalam format yang benar.',
    'required'              => 'Isian {field} tidak boleh kosong.',
    'required_with'         => 'Isian {field} diperlukan saat {param} hadir.',
    'required_without'      => 'Isian {field} diperlukan saat {param} tidak hadir.',
    'string'                => 'Isian {field} harus berupa string yang valid.',
    'timezone'              => 'Isian {field} harus berupa sebuah zona waktu yang valid.',
    'valid_base64'          => 'Isian {field} harus berupa sebuah string base64 yang valid.',
    'valid_email'           => 'Isian {field} harus berisi sebuah alamat surel yang valid.',
    'valid_emails'          => 'Isian {field} harus berisi semua alamat surel yang valid.',
    'valid_ip'              => 'Isian {field} harus berisi sebuah IP yang valid.',
    'valid_url'             => 'Isian {field} harus berisi sebuah URL yang valid.',
    'valid_url_strict'      => 'Isian {field} harus berisi sebuah URL yang valid.',
    'valid_date'            => 'Isian {field} harus berisi sebuah tanggal yang valid.',
    'valid_json'            => 'Isian {field} harus berisi sebuah json yang valid.',

    // Credit Cards
    'valid_cc_num' => '{field} tidak tampak sebagai sebuah nomor kartu kredit yang valid.',

    // Files
    'uploaded' => '{field} bukan sebuah berkas diunggah yang valid.',
    'max_size' => '{field} terlalu besar dari sebuah berkas.',
    'is_image' => '{field} bukan berkas gambar diunggah yang valid.',
    'mime_in'  => '{field} tidak memiliki sebuah tipe mime yang valid.',
    'ext_in'   => '{field} tidak memiliki sebuah ekstensi berkas yang valid.',
    'max_dims' => '{field} bukan gambar, atau terlalu lebar atau tinggi.',
];
