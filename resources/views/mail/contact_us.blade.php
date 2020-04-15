<table style="border: 1px solid #ddd; border-collapse: collapse; width: 100%;">
    <tbody>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.name') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['name'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.email') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['email'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.tel') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['tel'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.message') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['message'] ?? null }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.accept') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ isset($values['accept']) && $values['accept'] == '1' ? __('f.Received') : __('f.Refusal') }}</td>
    </tr>
    <tr style="text-align: left;">
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ __('f.date') }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; font-size: 16px;">{{ $values['date'] ?? $values['date'] }}</td>
    </tr>
    </tbody>
</table>
