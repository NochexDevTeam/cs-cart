<div class="control-group">
    <label class="control-label" for="">{__("merchant_id")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][merchantid]" value="{$processor_params.merchantid}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="">{__("payment_details")}:</label>
    <div class="controls">
    	<input type="text" name="payment_data[processor_params][payment_description]" value="{$processor_params.payment_description}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="testmode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][testmode]" id="testmode">
            <option value="Y" {if $processor_params.testmode == "Y"}selected="selected"{/if}>{__("test")}</option>
            <option value="N" {if $processor_params.testmode == "N"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="hidemode">Hide Billing Details:</label>
    <div class="controls">
        <select name="payment_data[processor_params][hidemode]" id="hidemode">
            <option value="Y" {if $processor_params.hidemode == "Y"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="N" {if $processor_params.hidemode == "N"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="detailmode">Detailed Product Information:</label>
    <div class="controls">
        <select name="payment_data[processor_params][detailmode]" id="detailmode">
            <option value="Y" {if $processor_params.detailmode == "Y"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="N" {if $processor_params.detailmode == "N"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="postmode">Separate Postage Total:</label>
    <div class="controls">
        <select name="payment_data[processor_params][postmode]" id="postmode">
            <option value="Y" {if $processor_params.postmode == "Y"}selected="selected"{/if}>{__("Yes")}</option>
            <option value="N" {if $processor_params.postmode == "N"}selected="selected"{/if}>{__("No")}</option>
        </select>
    </div>
</div>
