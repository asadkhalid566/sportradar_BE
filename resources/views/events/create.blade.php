<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" >
                        <small class="text-danger error-title"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sport</label>
                        <select name="_sport_id" class="form-select" >
                            <option value="">Select Sport</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger error-_sport_id"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <select name="_location_id" class="form-select">
                            <option value="">Select Location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger error-_location_id"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Team 1</label>
                            <select name="team1_id" class="form-select" >
                                <option value="">Select Team 1</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-team1_id"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Team 2</label>
                            <select name="team2_id" class="form-select" >
                                <option value="">Select Team 2</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-team2_id"></small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Start Date & Time</label>
                        <input type="datetime-local" name="start_time" class="form-control" >
                        <small class="text-danger error-start_time"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                        <small class="text-danger error-description"></small>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
