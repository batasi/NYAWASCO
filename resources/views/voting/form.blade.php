<form action="{{ route('flutterwave.initiate') }}" method="POST">
    @csrf
    <input type="hidden" name="nominee_id" value="{{ $nominee->id }}">
    <input type="hidden" name="voting_contest_id" value="{{ $contest->id }}">
    <div class="form-group">
        <label>Phone Number (M-PESA)</label>
        <input type="text" name="phone_number" class="form-control" required placeholder="2547XXXXXXXX">
    </div>
    <div class="form-group">
        <label>Number of Votes</label>
        <input type="number" name="votes_count" class="form-control" min="1" required>
    </div>
    <button type="submit" class="btn btn-success mt-3">Vote Now (Pay via M-PESA)</button>
</form>
