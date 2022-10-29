var ctiy = document.getElementById("ctiy");
var button = document
    .getElementById("btnSeacher")
    .addEventListener("click", () => {
        vectorLayer.setStyle(styleFunction);
        ctiy.value.length
            ? $.ajax({
                type: "POST",
                url: "CMR_pgsqlAPI.php",
                data: {
                    name: ctiy.value,
                },

                success: function (result, status, erro) {
                    console.log("abc");
                    if (result == "null") alert("không tìm thấy đối tượng");
                    else console.log(result);
                    highLightObj(result);
                },
                error: function (req, status, error) {
                    alert(req + " " + status + " " + error);
                },
            })
            : alert("Nhập dữ liệu tìm kiếm");
    });
