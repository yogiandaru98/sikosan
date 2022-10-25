<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FotoKosanModel;
use App\Models\KosanModel;

use function PHPUnit\Framework\fileExists;

class KosanController extends BaseController
{

    // CONSTRUCTOR
    public function __construct()
    {
        $this->kosanModel = new KosanModel();
        $this->fotoKosanModel = new FotoKosanModel();
    }

    // INDEX
    public function index()
    {
        
        return view('auth/admin/data_kosan_page');
    }

    // CREATE DATA KOSAN
    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Kosan | Owner',
        ];

        return view('auth/owner/tambah_kosan_page', $data);
    }

    // SAVE CREATE DATA KOSAN
    public function save()
    {
        // Ambil data kosan dari input
        $data_kosan = [
            'namaKost' => $this->request->getVar('namaKost'),
            'alamat' => $this->request->getVar('alamat'),
            'kota' => $this->request->getVar('kota'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'fasilitas' => $this->request->getVar('fasilitas'),
            'harga' => $this->request->getVar('harga'),
            'type' => $this->request->getVar('type'),
            'idPemilik' => user_id(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // validasi data kosan
        // $validated = [
        //     'namaKost' => [
        //         'rules' => 'required|min_length[5]|max_length[50]',
        //         'errors' => [
        //             'required' => 'Nama kosan harus diisi',
        //             'min_length' => 'Nama kosan minimal 5 karakter',
        //             'max_length' => 'Nama kosan maksimal 50 karakter',
        //         ]
        //     ],
        //     'alamat' => [
        //         'rules' => 'required|min_length[5]',
        //         'errors' => [
        //             'required' => 'Alamat kosan harus diisi',
        //             'min_length' => 'Alamat kosan minimal 5 karakter',
        //         ]
        //     ],
        //     'kota' => [
        //         'rules' => 'required|inlist[Lampung Barat,Tanggamus,Lampung Selatan,Lampung Timur,Lampung Tengah,Lampung Utara,Way Kanan,Pesawaran,Tulang Bawang Barat,Tulang Bawang,Pesisir Barat,Bandar Lampung,Metro]',
        //         'errors' => [
        //             'required' => 'Kecamatan kosan harus diisi',
        //             'inlist' => 'Kota/Kabupaten kosan harus dipilih dengan benar',
        //         ]
        //     ],
        //     'deskripsi' => [
        //         'rules' => 'required|min_length[5]',
        //         'errors' => [
        //             'required' => 'Deskripsi kosan harus diisi',
        //             'min_length' => 'Deskripsi kosan minimal 5 karakter',
        //         ]
        //     ],
        //     'fasilitas' => [
        //         'rules' => 'required',
        //         'errors' => [
        //             'required' => 'Fasilitas kosan harus diisi',
        //         ]
        //     ],
        //     'harga' => [
        //         'rules' => 'required|numeric',
        //         'errors' => [
        //             'required' => 'Harga kosan harus diisi',
        //             'numeric' => 'Harga kosan harus berupa angka',
        //         ]
        //     ],
        //     'type' => [
        //         'rules' => 'required|inlist[Putra,Putri]',
        //         'errors' => [
        //             'required' => 'Type kosan harus diisi',
        //             'inlist' => 'Type kosan harus dipilih dengan benar',
        //         ]
        //     ],
        // ];

        // // jika validasi error
        // if (!$validated) {
        //     return redirect()->to('/onlyOwners/create')->withInput();
        // }

        // Insert data kosan ke database
        $this->kosanModel->insert($data_kosan);

        // mengambil id kosan yang baru saja diinputkan
        $idKosanInsert = $this->kosanModel->getInsertID();

        // Mengambil data foto kosan dari input form
        $foto_1 = $this->request->getFile('foto_1');
        $name_foto1 = $foto_1->getRandomName();
        $foto_2 = $this->request->getFile('foto_2');
        $foto_3 = $this->request->getFile('foto_3');

        $data_foto_kosan = [
            'id_kosan' => $idKosanInsert,
            'nama_foto' => $name_foto1,
        ];

        $this->fotoKosanModel->insert($data_foto_kosan);

        // Memindahkan foto kosan ke local storage
        $foto_1->move( '../public/foto_kosan', $name_foto1);

        if ($foto_2->getName() !== '') {
            $name_foto2 = $foto_2->getRandomName();
            $data_foto_kosan = [
                'id_kosan' => $idKosanInsert,
                'nama_foto' => $name_foto2,
            ];

            $this->fotoKosanModel->insert($data_foto_kosan);

            $foto_2->move( '../public/foto_kosan', $name_foto2);
        }

        if ($foto_3->getName() !== '') {
            $name_foto3 = $foto_3->getRandomName();
            $data_foto_kosan = [
                'id_kosan' => $idKosanInsert,
                'nama_foto' => $name_foto3,
            ];
            $this->fotoKosanModel->insert($data_foto_kosan);
            $foto_3->move('../public/foto_kosan', $name_foto3);
        }

        return redirect()->to('/owner/kosan_anda');
    }

    // DELETE DATA KOSAN
    public function delete($id_kosan)
    {
        $imageFile = $this->fotoKosanModel->find($id_kosan);
        $imageName = $imageFile['nama_foto'];

        if (fileExists('public/foto_kosan/' . $imageName)) {
            unlink('../public/foto_kosan/' . $imageName);
        }

        $this->kosanModel->delete($id_kosan);

        return redirect()->to('/owner/kosan_anda');
    }

    // EDIT DATA KOSAN
    public function edit($id_kosan)
    {
        $data = [
            'title' => 'Ubah Data Kosan',
            'kosan' => (new KosanModel())->where('id_kosan', $id_kosan)->first(),
            'foto' => (new FotoKosanModel())->where('id_kosan', $id_kosan)->findAll(),
        ];
        return view('auth/owner/edit_kosan_page', $data);
    }

    // UPDATE EDITED DATA KOSAN
    public function update()
    {
        $jumlahFoto = count($this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll());

        $data = [
            'namaKost' => $this->request->getVar('namaKost'),
            'alamat' => $this->request->getVar('alamat'),
            'kota' => $this->request->getVar('kota'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'fasilitas' => $this->request->getVar('fasilitas'),
            'harga' => $this->request->getVar('harga'),
            'type' => $this->request->getVar('type'),
            'idPemilik' => user_id(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->kosanModel->update($this->request->getVar('id_kosan'), $data);
        if ($this->request->getFile('foto_1')->getName() !== '') {

            $foto = $this->request->getFile('foto_1');
            $namaFoto = $foto->getRandomName();
            unlink( '../public/foto_kosan/' . $this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[0]['nama_foto']);
            $data_foto = [
                'nama_foto' => $namaFoto,
                'id_kosan' => $this->request->getVar('id_kosan'),
            ];
            $this->fotoKosanModel->update($this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[0]['id_foto'], $data_foto);
            $foto->move( '../public/foto_kosan', $namaFoto);
        }
        if ($this->request->getFile('foto_2')->getName() !== '') {
            $foto = $this->request->getFile('foto_2');
            $namaFoto = $foto->getRandomName();
            $data_foto = [
                'nama_foto' => $namaFoto,
                'id_kosan' => $this->request->getVar('id_kosan'),
            ];
            if ($jumlahFoto > 1) {
                unlink( '../public/foto_kosan/' . $this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[1]['nama_foto']);
                $this->fotoKosanModel->update($this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[1]['id_foto'], $data_foto);
            } else {
                $this->fotoKosanModel->insert($data_foto);
            }
            $foto->move( '../public/foto_kosan', $namaFoto);
        }
        if ($this->request->getFile('foto_3')->getName() !== '') {
            $foto = $this->request->getFile('foto_3');
            $namaFoto = $foto->getRandomName();
            $data_foto = [
                'nama_foto' => $namaFoto,
                'id_kosan' => $this->request->getVar('id_kosan'),
            ];
            if ($jumlahFoto > 2) {
                unlink( '../public/foto_kosan/' . $this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[2]['nama_foto']);
                $this->fotoKosanModel->update($this->fotoKosanModel->where('id_kosan', $this->request->getVar('id_kosan'))->findAll()[2]['id_foto'], $data_foto);
            } else {
                $this->fotoKosanModel->insert($data_foto);
            }

            $foto->move( '../public/foto_kosan', $namaFoto);
        }
        return redirect()->to('/owner/kosan_anda');
    }
}
