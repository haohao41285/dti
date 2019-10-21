<form>
<div class="form-group col-md-12 row">
    <div class="col-md-2">
        <label for="">Category</label>
        <select id="category" name="category" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            @foreach (getCategory() as $key =>  $category)
                <option value="{{$key}}">{{$category}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="">Service</label>
        <select id="service_id" name="service_id" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            @foreach ($service_list as $key =>  $service)
                <option value="{{$service->id}}">{{$service->cs_name}}</option>
            @endforeach
        </select>
    </div>
    @if(\Illuminate\Support\Facades\Auth::user()->user_id == 1)
    <div class="col-md-2">
        <label for="">Assignee</label>
        <select id="assign_to" name="assign_to" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            @foreach ($user_list as $key => $user)
                <option value="{{$user->user_id}}">{{$user->getFullname()}}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="col-md-2">
        <label for="">Priority</label>
        <select id="priority" name="priority" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            @foreach (getPriorityTask() as $key => $priority)
                <option value="{{$key}}">{{$priority}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label for="">Status</label>
        <select id="status" name="status" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            @foreach (getStatusTask() as $key =>  $status)
                <option value="{{$key}}">{{$status}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-2 " style="position: relative;">
        <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="formReset" value="Reset">
        </div>
    </div>
</div>
</form>
