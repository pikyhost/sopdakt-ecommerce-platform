@php
    if (!isset($attributes['id'])) {
        $attributes['id'] = $attributes['name'];
    }
    if (!isset($attributes['label-name'])) {
        $attributes['label-name'] = __(ucwords(str_replace('_', ' ', $attributes['name'])));
    }
    if (!isset($attributes['required'])) {
        $attributes['required'] = false;
    }
@endphp
<label for="{{$attributes['id']}}" style="float: {{app()->getLocale() == 'ar' ? 'right' : ''}}">{{__($attributes['label-name'])}}  @if($attributes['required'])
        <span class="required text-danger">*</span>
    @endif</label>
<select {{$attributes->merge(['class'=>'form-control'])}}>
    <option value="">{{__('Select')}} {{__($attributes['label-name'])}}</option>
    {{ $slot}}
</select>
@error($attributes['name'])
<div class="text-danger">{{ $message }}</div>
@enderror
