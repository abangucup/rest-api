<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get data items
        $items = Item::latest()->paginate(5);

        // return nilai ItemResource dengan paramater
        return new ItemResource(true, 'List Items', $items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // simpan item
        $validate = Validator::make($request->all(), [
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,scg|max:2048',
            'judul' => 'required',
            'keterangan' => 'required'
        ]);
        // cek validasi input
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        // upload gambar
        $gambar = $request->file('gambar');
        $gambar->storeAs('public/posts', $gambar->hashName());

        // create item
        $item = Item::create([
            'gambar' => $gambar->hashName(),
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
        ]);

        // return response
        return new ItemResource(true, 'Berhasil Menambahkan Item Baru', $item);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return new ItemResource(true, 'Data Ditemukan', $item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //validasi alur
        $validate = Validator::make($request->all(), [
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,scg|max:2048',
            'judul' => 'required',
            'keterangan' => 'required'
        ]);
        // cek validasi input
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        if ($request->hasFile('gambar')) {
            // Upload Image
            $gambar = $request->file('gambar');
            $gambar->storeAs('public/posts', $gambar->hashName());

            // hapus gambar lama
            Storage::delete('public/items'.$item->gambar);
            // create item
            $item->update([
                'gambar' => $gambar->hashName(),
                'judul' => $request->judul,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            $item->update([
                'judul' => $request->judul,
                'keterangan' => $request->keterangan
            ]);
        }

        return new ItemResource(true, 'Berhasil Merubah Item', $item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        // hapus gambar
        Storage::delete('app/public/posts/',$item->gambar);

        // Hapus item
        $item->delete();

        return new ItemResource(true, 'Berhasil Menghapus Item', null);
    }
}
