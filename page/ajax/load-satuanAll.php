<?php
// memanggil file config.php

    include('../db/config.php');
    include('../db/function.php'); /// berisi fungsi-fungsi input/output formatting
    // session_start();
    // cekAksesUser($database, 'mitra_utama');

    // Alternative SQL join in Datatables
    $id_table = 'kode_user';

    $columns = array(
        'id_satuan',
        'nama_satuan',
    );

    $columnsFront = array(
        'id_satuan',
        'nama_satuan',
    );

    $columns_hide = array(
        // 'foto2',
    );

    $from = 'tb_satuan ';

    $id_table_edited = $id_table != '' ? $id_table . ',' : '';
    // custom SQL
    $sql = "SELECT ".implode(',', $columns)." FROM {$from} ";

    // search
    if (isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        $search = $_POST['search']['value'];
        $where  = " WHERE ";
        // create parameter pencarian kesemua kolom yang tertulis
        // di $columns
        for ($i=0; $i < count($columns); $i++) {
            $where .= $columns[$i] . ' LIKE "%'.$search.'%"';

            // agar tidak menambahkan 'OR' diakhir Looping
            if ($i < count($columns)-1) {
                $where .= ' OR ';
            }
        }

        $sql .= ' ' . $where;
    }

    // $sql .= " GROUP BY {$id_table} ";

    //SORT Kolom
    $sortColumn = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 1;
    $sortDir    = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    $sortColumn = $columnsFront[$sortColumn-1];

    $sql .= " ORDER BY {$sortColumn} {$sortDir}";

    // var_dump($sql);
    // echo $sql;
    $count = $database->query($sql);
    // hitung semua data
    $totaldata = $count->num_rows;

    $count->close();

    // memberi Limit
    $start  = isset($_POST['start']) ? $_POST['start'] : 0;
    $length = isset($_POST['length']) ? $_POST['length'] : 10;


    $sql .= " LIMIT {$start}, {$length}";

    $data  = $database->query($sql);

    // create json format
    $datatable['draw']            = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $datatable['recordsTotal']    = $totaldata;
    $datatable['recordsFiltered'] = $totaldata;
    $datatable['data']            = array();

    $no = $start+1;
    while ($row = $data->fetch_array()) {

        $fields = array($no++);
        // $fields = $row['nama_barang'];
        // var_dump($fields);die;
        $fields[] = $row['id_satuan'];
        $fields[] = $row['nama_satuan'];

        for ($i=0; $i < count($columns); $i++) {



            // $fields[] = $row["{$columns[$i]}"];
                // var_dump($row);die;
    // echo $sql;
        }
        $aksi = '
        <button href="#" class="btn btn-warning btn-sm mb-1 tombolEdit" data-nama_satuan="'.$row['nama_satuan'].'" data-id_satuan="'.$row['id_satuan']. '" ">
            <i class="glyphicon glyphicon-pencil"></i> EDIT
        </button>
        <button href="#" class="btn btn-danger btn-sm mb-1 tombolHapus" data-nama_satuan="'.$row['nama_satuan'].'" data-id_satuan="'.$row['id_satuan']. '" ">
        <i class="glyphicon glyphicon-trash"></i> HAPUS
        </button>
        ';
        $fields[] = $aksi;


        $datatable['data'][] = $fields;
    }

    $data->close();
    echo json_encode($datatable);
