<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Electrical_parts;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AdminController extends Controller
{
    

    public function AddElectrical(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'size' => 'required|integer',
            'warning' => 'required|string',
            'notes' => 'nullable|string',
            'way_of_work' => 'nullable|string',
        ]);
        if ($validator->fails()) {

            return response()->json($validator->errors(), 422);
        }
        $item = Electrical_parts::create([
            'name' => $request->input('name'),
            'size' => $request->input('size'),
            'warning' => $request->input('warning'),
            'notes' => $request->input('notes'),
            'way_of_work' => $request->input('way_of_work'),
        ]);

        $qrCode = QrCode::size(200)->generate($item->id);

        return response()->json([
            'message' => 'تم إنشاء السجل بنجاح',
            'item' => $item,
            'qr_code' => base64_encode($qrCode),
        ], 201);
    }

}
