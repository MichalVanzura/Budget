<table class="table table-hover">
    <?php
    foreach ($table as $row) {
        if (isset($next_level)) {
            echo '<tr  class="clickableRow" href="/' . $next_level . $row['kod'] . '">';
        } else {
            echo '<tr>';
        }
        
        foreach ($row as $key => $value) {
            if ($key == 'castka') {
                echo '<td class="text-right">' . formatAmount($value) . '</td>';
            } else {
                echo '<td>' . ucfirst(mb_strtolower($value)) . '</td>';
            }
        }
    echo '</tr>';
    } ?>
</table>

<script>
    $(document).ready(function ($) {
        $(".clickableRow").click(function () {
            window.document.location = $(this).attr("href");
        });
    });
</script>
