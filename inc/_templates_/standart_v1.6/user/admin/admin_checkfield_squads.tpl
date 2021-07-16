<tr>
    <td><input class="checkbox" type="checkbox" id="squad_{$id}" name="squad{$id}" value="{$id}" {$check} /><label for="squad_{$id}"> {$squad}</label></td>
    <td align="center">
        <select name="sqpos{$id}" class="selectpicker">
            {lang msgID="user_noposi"}
            {$eposi}
        </select>
    </td>
</tr>