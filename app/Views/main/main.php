<html>
    <head>
        <title>Corekara - Weather Forecast</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="<?=base_url('assets/css/style.css')?>">
    </head>
    <body class="scroll-lock">
        <div id="loader_box" class="d-flex justify-content-center loader-container hidden">
            <div class="spinner-border loader" role="status">
            </div>
        </div>
        <div id="wrapper" class="wrapper container">
            <form method="POST" id="postcode_form">
                <div id="form-row" class="row center-form">
                    <div class="col-2"  style="padding: 5px 3.3vw;">
                        <label class="form-label">Post Code</label>
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="postcode" placeholder="160-0022" pattern="([0-9]{3})(-)([0-9]{4})" required title="post code should only contain seven numbers from 0-9 with - after the third number">
                    </div>
                    <div class="col-4">
                        <input type="submit" class="form-control btn btn-primary">
                    </div>
                </div>
            </form>
            <br>
            <div id="refresh-box" class="hidden">
                <div class="row">
                    <div class="col-12" style="padding: 0 3.3vw;">
                        <h1 id="address_header">Tokyo, Shinjuku City, Shinjuku</h1>
                    </div>
                </div>
                <br>
                <div class="row forecast-row">
                    <div class="col-12"  style="padding: 0 3.3vw;">
                        <h2 class="form-label">3-day forecast</h2>
                    </div>
                    <?php
                    for ($i = 0; $i < 3; $i++) {
                    ?>
                    <div class="col-sm">
                        <div class="row forecast-container">
                            <div class="col-12 forecast-image-container">
                                <img id="forecast_image_<?=$i?>" class="forecast-image" src="<?=base_url('assets/images/sample.jpeg')?>">
                            </div>
                            <div class="col-12 text-center">
                                <p id="forecast_date_<?=$i?>" class="form-label">2019-08-24 Sat</p>
                            </div>
                            <div class="col-12 text-center">
                                <h2 id="forecast_weather_<?=$i?>">Sunny</h2>
                            </div>
                            <div class="col-6 text-center">
                                <p id="forecast_max_<?=$i?>" class="form-label">Max: 35°</p>
                            </div>
                            <div class="col-6 text-center">
                                <p id="forecast_min_<?=$i?>" class="form-label">Min: 30°</p>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                <br>
                <div class="row no-margin">
                    <div class="col-md">
                        <div class="row map-row">
                            <div class="col-12 no-padding">
                                <h2 class="form-label">Map</h2>
                            </div>
                            <div class="col-12 map-container">
                                <iframe
                                id="map_frame"
                                width="100%"
                                height="100%"
                                style="border:0"
                                loading="lazy"
                                allowfullscreen
                                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBPhDeaHVnMYcQrmGVpWdP-SnXDyRMwe-k&q=shinjuku">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="row map-row">
                            <div class="col-12 no-padding">
                                <h2 class="form-label">Popular Locations</h2>
                            </div>
                            <div class="col-12 map-container">
                                <div id="article_row" class="row article-row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function(){
                $("#postcode_form").submit(function(e){
                    e.preventDefault(e);
                    $("body").addClass('scroll-lock');
                    $("#loader_box").removeClass("hidden");
                    var formData = $(this).serializeArray();
                    $.post("<?=base_url('API/submit_post_code')?>", formData, function(data){
                        jsonData = $.parseJSON(data);
                        if(jsonData.status){
                            addData = jsonData.data;
                            forecastData = addData.forecast;
                            hotspotData = addData.hotspot;
                            $("#address_header").text(addData.address);
                            $("#map_frame").attr("src", "https://www.google.com/maps/embed/v1/place?key=AIzaSyBPhDeaHVnMYcQrmGVpWdP-SnXDyRMwe-k&q=" + addData.address);
                            $("#article_row").html('');
                            $(forecastData).each(function(index, value) {
                                $("#forecast_image_" + index).attr("src", value.icon);
                                $("#forecast_date_" + index).text(value.date);
                                $("#forecast_weather_" + index).text(value.weather);
                                $("#forecast_max_" + index).text("Max: " + value.max + "°");
                                $("#forecast_min_" + index).text("Min: " + value.min + "°");
                            });
                            $(hotspotData).each(function(index, value) {
                                var html = '<div class="col-12 article-container">';
                                html += '<a href="https://maps.google.com/?q=' + value.lat + ',' + value.lng + '" target="_blank">';
                                html += '<div class="row article-content-row">';
                                html += '<div class="col-9 article-content-container">';
                                html += '<p class="article-title">' + value.name + '(' + value.title + ')</p>';
                                html += '<p class="article-date">' + value.description + '</p>';
                                html += '</div>';
                                html += '<div class="col-3 article-image-container">';
                                html += '<img class="article-image" src="' + value.image +'">';
                                html += '</div>';
                                html += '</div>';
                                html += '</a>';
                                html += '</div>';
                                $("#article_row").append(html);
                            });
                            $("#refresh-box").removeClass('hidden');
                            $("#form-row").removeClass('center-form');
                            $(".custom-container").removeClass('no-padding');
                            $(".custom-container").removeClass('no-margin');
                            $("body").removeClass('scroll-lock');
                            $("#loader_box").addClass("hidden");
                            $("#wrapper").addClass("custom-container");
                        } else {
                            alert(jsonData.message);
                            $("body").removeClass('scroll-lock');
                            $("#loader_box").addClass("hidden");
                        }
                    });
                });
            });
        </script>
    </body>
</html>