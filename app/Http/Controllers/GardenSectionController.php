<?php

namespace App\Http\Controllers;

use App\Models\GardenProject;
use App\Models\GardenSection;
use App\Models\SectionElement;
use Illuminate\Http\Request;

class GardenSectionController extends Controller
{
    public function store(Request $request, GardenProject $gardenProject)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['garden_project_id'] = $gardenProject->id;
        $data['order'] = $gardenProject->sections()->max('order') + 1;

        $section = GardenSection::create($data);

        return redirect()->route('garden-sections.editor', [$gardenProject, $section])
            ->with('success', 'Sekcja została dodana');
    }

    public function editor(GardenProject $gardenProject, GardenSection $gardenSection)
    {
        $gardenSection->load('elements');
        return view('garden-sections.editor', compact('gardenProject', 'gardenSection'));
    }

    public function update(Request $request, GardenProject $gardenProject, GardenSection $gardenSection)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $gardenSection->update($data);

        return back()->with('success', 'Sekcja została zaktualizowana');
    }

    public function saveCanvas(Request $request, GardenProject $gardenProject, GardenSection $gardenSection)
    {
        $data = $request->validate([
            'canvas_data' => 'required|array',
        ]);

        $gardenSection->update(['canvas_data' => $data['canvas_data']]);

        return response()->json(['ok' => true]);
    }

    public function destroy(GardenProject $gardenProject, GardenSection $gardenSection)
    {
        $gardenSection->delete();

        return redirect()->route('garden-projects.show', $gardenProject)
            ->with('success', 'Sekcja została usunięta');
    }

    // --- Elements ---

    public function storeElement(Request $request, GardenProject $gardenProject, GardenSection $gardenSection)
    {
        $data = $request->validate([
            'type'       => 'required|string|max:100',
            'name'       => 'required|string|max:255',
            'zone_ref'   => 'nullable|string|max:120',
            'zone_label' => 'nullable|string|max:255',
            'material'   => 'nullable|string|max:255',
            'quantity'   => 'required|numeric|min:0',
            'unit'       => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $data['garden_section_id'] = $gardenSection->id;
        $data['order'] = $gardenSection->elements()->max('order') + 1;

        SectionElement::create($data);

        return back()->with('success', 'Element został dodany');
    }

    public function updateElement(Request $request, GardenProject $gardenProject, GardenSection $gardenSection, SectionElement $element)
    {
        $data = $request->validate([
            'type'       => 'required|string|max:100',
            'name'       => 'required|string|max:255',
            'zone_ref'   => 'nullable|string|max:120',
            'zone_label' => 'nullable|string|max:255',
            'material'   => 'nullable|string|max:255',
            'quantity'   => 'required|numeric|min:0',
            'unit'       => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $element->update($data);

        return back()->with('success', 'Element został zaktualizowany');
    }

    public function destroyElement(GardenProject $gardenProject, GardenSection $gardenSection, SectionElement $element)
    {
        $element->delete();

        return back()->with('success', 'Element został usunięty');
    }
}
