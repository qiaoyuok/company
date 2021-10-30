$(document).ready(function () {
    $(".chosen-select").chosen({disable_search_threshold: 10});

    function getEmployeeList(teamId = 0) {
        $.get('/employee/get-employee-list?teamId=' + teamId, function (res) {
            resetChosen(res)
        })
    }

    function resetChosen(data) {
        $(".chosen-employee-list").empty();
        data.map(function (item) {
            $(".chosen-employee-list").append("<option value=" + item.id + ">" + item.employee + "</option>")
        })
        $(".chosen-employee-list").trigger('chosen:updated')
    }

    $('.chosen-team-change').change(function (res, e) {
        console.log(res, e, 889);
        if (e.selected) {
            getEmployeeList(e.selected)
        }else{
            resetChosen([])
        }
    })
})