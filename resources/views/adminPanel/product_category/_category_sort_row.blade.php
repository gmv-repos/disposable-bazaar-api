@foreach ($categories as $category)
    @php
        $sortOrders = $category->sortOrders->keyBy('section_name');
    @endphp

    <tr>
        <td style="padding-left: {{ $level * 25 }}px;">
            <span class="@if ($level == 1) text-primary @endif">
                {{ $category->name }}
            </span>
        </td>

        {{-- HEADER DROPDOWN --}}
        <td>
            <input type="number" name="sorting[{{ $category->id }}][headerDropdown][sort_order]"
                value="{{ old("sorting.$category->id.headerDropdown.sort_order", $sortOrders['headerDropdown']->sort_order ?? '') }}"
                class="form-control form-control-sm">

            <label class="mt-1 d-flex align-items-center gap-1">
                <input type="checkbox" name="sorting[{{ $category->id }}][headerDropdown][is_visible]" value="1"
                    {{ isset($sortOrders['headerDropdown']) && $sortOrders['headerDropdown']->is_visible ? 'checked' : '' }}>
                Show
            </label>
        </td>

        {{-- ONE STOP SHOP --}}
        <td>
            <input type="number" name="sorting[{{ $category->id }}][oneStopShop][sort_order]"
                value="{{ old("sorting.$category->id.oneStopShop.sort_order", $sortOrders['oneStopShop']->sort_order ?? '') }}"
                class="form-control form-control-sm">

            <label class="mt-1 d-flex align-items-center gap-1">
                <input type="checkbox" name="sorting[{{ $category->id }}][oneStopShop][is_visible]" value="1"
                    {{ isset($sortOrders['oneStopShop']) && $sortOrders['oneStopShop']->is_visible ? 'checked' : '' }}>
                Show
            </label>
        </td>

        {{-- PRODUCTS SLIDER TOP --}}
        <td>
            <input type="number" name="sorting[{{ $category->id }}][productsSliderTop][sort_order]"
                value="{{ old("sorting.$category->id.productsSliderTop.sort_order", $sortOrders['productsSliderTop']->sort_order ?? '') }}"
                class="form-control form-control-sm">

            <label class="mt-1 d-flex align-items-center gap-1">
                <input type="checkbox" name="sorting[{{ $category->id }}][productsSliderTop][is_visible]"
                    value="1"
                    {{ isset($sortOrders['productsSliderTop']) && $sortOrders['productsSliderTop']->is_visible ? 'checked' : '' }}>
                Show
            </label>
        </td>
    </tr>

    {{-- Recursive children --}}
    @if ($category->childCategories->isNotEmpty())
        @include('adminPanel.product_category._category_sort_row', [
            'categories' => $category->childCategories,
            'level' => $level + 1,
        ])
    @endif
@endforeach
