<div class="row mb-3">
    <div class="col-md-4">
        <label for="property_floor">Floor</label>
        <select id="property_floor" name="property_floor" class="form-select">
            <option value=""></option>
            <option value="Lower Basement">Lower Basement</option>
            <option value="Upper Basement">Upper Basement</option>
            <option value="Ground">Ground</option>
            @for($i=1; $i<=100; $i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>    
    </div>

    <div class="col-md-4">
        <label for="property_room">Rooms</label>
        <select id="property_room" name="property_room" class="form-select">
            <option value=""></option>
            @for($i=1; $i<=10; $i++)
            <option value="{{$i}} BHK">{{$i}} BHK</option>
            @endfor
        </select>    
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="property_bed">Bedroom</label>
        <select id="property_bed" name="property_bed" class="form-select">
            <option value=""></option>
            @for($i=1; $i<=10; $i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>    
    </div>

    <div class="col-md-4">
        <label for="property_bath">Bathroom</label>
        <select id="property_bath" name="property_bath" class="form-select">
            @for($i=0; $i<=5; $i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>    
    </div>

    <div class="col-md-4">
        <label for="property_balcony">Balcony</label>
        <select id="property_balcony" name="property_balcony" class="form-select">
            @for($i=0; $i<=5; $i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>    
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="property_area">Property Area (SqFt)</label>
        <input type="test" id="property_area" name="property_area" class="form-control number">
    </div>
    <div class="col-md-8">
        <label for="property_parking">Parking</label>
        <div class="mt-2" id="property_parking_area">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="property_parking" value="YES"> Yes
            </div>
            <div class="form-check form-check-inline">    
                <input class="form-check-input" type="radio" name="property_parking" value="NO"> No
            </div>
        </div>  
    </div>

</div>   

<div class="row mb-3">
    <div class="col-md-12">
        <label for="property_address">Address of the Property</label>
        <input type="test" id="property_address" name="property_address" class="form-control" maxlength="200">
    </div>
</div>    

<div class="row mb-3">
    <div class="col-md-4">
        <label for="property_city">City</label>
        <input type="test" id="property_city" name="property_city" class="form-control" maxlength="100">
    </div>
    <div class="col-md-4">
        <label for="property_state">State</label>
        <input type="test" id="property_state" name="property_state" class="form-control" maxlength="100">
    </div>
    <div class="col-md-4">
        <label for="property_pin">PIN Code</label>
        <input type="test" id="property_pin" name="property_pin" class="form-control number" maxlength="6">
    </div>
</div>  