<p>Số lượng errors: <?php echo $totalErrors; ?></p>

<p>Số errors / số giao dịch: <?php echo $totalErrors; ?>/<?php echo $count;?></p>

<p>Danh sách lỗi: </p>

<table>
    <tr>
        <th>Username</th>
        <th>Cate Code</th>
        <th>Card Code</th>
        <th>card Serial</th>
        <th>Response Code</th>
        <th>Message</th>
        <th>Date Created</th>
    </tr>
    <?php foreach ($errorInfo as $row) { ?>
        <tr>
            <td><?php echo $row['username']?></td>
            <td><?php echo $row['cate_code']?></td>
            <td><?php echo $row['card_code']?></td>
            <td><?php echo $row['card_serial']?></td>
            <td><?php echo $row['response_code']?></td>
            <td><?php echo $row['msg']?></td>
            <td><?php echo $row['date_created']?></td>
        </tr>
    <?php } ?>
</table>
