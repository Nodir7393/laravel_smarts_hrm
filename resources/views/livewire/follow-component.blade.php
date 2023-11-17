<div>
    <form wire:submit.prevent="form">
        <div class="d-flex justify-content-between mb-2">
            <label class="mr-3">
                <x-select wire:model="bot" :options={{ $bot }} name="bot" label="Bot" m='' w="75"></x-select>
            </label>
        </div>
        <div class="input-group mb-3">
            <input wire:model="count" type="number" class="form-control" aria-label="number" aria-describedby="basic-addon1" min="1" required>
        </div>
        <button type="submit" class="btn btn-dark">Send</button>
    </form>
</div>
