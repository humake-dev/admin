function create_canvas(cnt_cell/* 하단 범주 */, data/*데이타 */, data_min/* 왼쪽 범주의 최저값 */, data_max/*왼쪽 범주의 최고값 */, eksdnl /*단위 */, number_of_line /*가로라인*/) {
	var canvas = document.getElementById('myCanvas');
	var width = canvas.width;
	var height = canvas.height;
	var context = canvas.getContext('2d');
	var margin_left = 60;
	//왼쪽 마진

	var margin_bottom = 40;
	//하단 마진

	var margin_right = 50;
	//오른쪽 마진

	var margin_top = 30;
	//상단 마진

	var margin_top_graph = 20;
	//그래프 위에 조금 남은부분
	var start_y = margin_top + margin_top_graph;
	var start_x = margin_left;
	var end_y = height - margin_bottom;
	var end_x = width - margin_right;

	var total_height = height - margin_top - margin_bottom - margin_top_graph;
	// 그래프 범위세로
	var total_width = width - margin_left - margin_right;
	//그래프 범위 가로

	var cnt_row = [];
	data_max=parseFloat(data_max);
	data_min=parseFloat(data_min);
	var a_data = (data_max -data_min)/number_of_line;

		for ( i = 0; data_min+a_data * i <= data_max; i++) {
			cnt_row.push((data_min+a_data * i).toFixed(1) + "");
	
		}



	context.beginPath();
	context.translate(0.5, 0.5);
	///////////////////백그라운드///////////////////
	context.rect(0, 0, width, height);

	context.fillStyle = "#FFFFFF";
	context.fill();

	context.strokeStyle = '#000';
	context.lineWidth = 7;
	context.stroke();
	///////////////////백그라운드///////////////////

	//////////////////그래프 기본 선//////////////////////
	context.moveTo(0 + margin_left, 0 + margin_top + margin_top_graph);
	context.lineTo(0 + margin_left, height - margin_bottom);
	//세로

	context.lineTo(width - margin_right, height - margin_bottom);
	//가로

	context.strokeStyle = '#c0c0c0';
	context.lineWidth = 1;
	context.stroke();
	//////////////////그래프 기본 선//////////////////////

	//////////////////그래프 내에 라인 들 그리기///////////////////
	context.beginPath();
	context.translate(0.5, 0.5);
	var each_row_height = total_height / (cnt_row.length - 1);
	var height_gap=a_data;
	height_gap=	each_row_height/a_data;

	//가로선들의 간격
	

	for ( i = 0; i < cnt_row.length - 1; i++) {
		context.moveTo(start_x, start_y + (i * each_row_height));
		context.lineTo(end_x, start_y + (i * each_row_height));
	}
	//가로선 그리기
	var cnt=cnt_cell.length;
	if(cnt<2)
	{
		cnt=2;
	}
	var each_cel_width= total_width / (cnt - 1);
	
					
	//세로선들 간격

	for ( i = 0; i < cnt_cell.length; i++) {
		context.moveTo(start_x + (i * each_cel_width), start_y);
		context.lineTo(start_x + (i * each_cel_width), end_y);
	}
	//세로선 그리기
	context.lineWidth = 0.5;
	context.strokeStyle = '#c0c0c0';
	context.stroke();

	//////////////////그래프 내에 라인 들 그리기///////////////////

	context.beginPath();
	context.translate(0.5, 0.5);
	////////////////왼쪽 범주 /////////////////////////
	for ( i = 0; i < cnt_row.length; i++) {

		context.fillStyle = '#c0c0c0';
		context.textAlign = 'right';
		context.font = '13pt Calibri';
		context.textBaseline = "bottom";
		context.fillText(cnt_row[i], start_x - 10, end_y - (i * each_row_height) + 5);
	}

	////////////////왼쪽 범주 /////////////////////////

	////////////////하단  범주 /////////////////////////
	for ( i = 0; i < cnt_cell.length; i++) {

		context.fillStyle = '#c0c0c0';
		context.textAlign = 'left';
		context.font = '11pt Calibri';
		context.textBaseline = "top";
		if(cnt_cell.length==1)
		{
			context.fillText(cnt_cell[i], start_x + (0.5 * each_cel_width) - 15, end_y + 10);						
		}
		else{
			context.fillText(cnt_cell[i], start_x + (i * each_cel_width) - 15, end_y + 10);
		}
	}

	////////////////하단  범주 /////////////////////////

	///////////////그래프 라인 그리기 /////////////////////

	for ( i = 0; i < data.length; i++) {

		if (data[i] == 0) {
			if (i == 0) {
				context.beginPath();
				context.moveTo(start_x + (i * each_cel_width), end_y);
			} else {
				context.lineTo(start_x + (i * each_cel_width), end_y);
			}

		} else {
			if (i == 0) {
				context.beginPath();
				context.moveTo(start_x + (i * each_cel_width), start_y + total_height-(data[i]-data_min)*height_gap);
			} else {
				context.lineTo(start_x + (i * each_cel_width), start_y + total_height-(data[i]-data_min)*height_gap);
			}

		}

	}

	context.lineWidth = 2;
	context.strokeStyle = '#c0c0c0';
	context.stroke();

	///////////////그래프 라인 그리기 /////////////////////

	///////////////점 그리기 ///////////////////////
	var point_size = 5;
	for ( i = 0; i < data.length; i++) {
		
		if (data[i] == 0) {

			context.beginPath();
			context.translate(0.5, 0.5);
			if(data.length==1)
			{
				context.arc(start_x + (0.5 * each_cel_width), end_y, point_size, 0, 2 * Math.PI, false);
			}
			else{
				context.arc(start_x + (i * each_cel_width), end_y, point_size, 0, 2 * Math.PI, false);
			}
			context.fillStyle = '#f01010';
			context.fill();

			// context.fillStyle = '#c0c0c0';
			// context.textAlign = 'left';
			// context.font = '11pt Calibri';
			// context.textBaseline = "top";
			// context.fillText(cnt_cell[i], start_x + (i * each_cel_width), end_y + 10);
			// context.lineWidth = point_size;
			// context.strokeStyle = 'red';
			// context.stroke();

		} else {
		
		
			context.beginPath();
			context.translate(0.5, 0.5);
			if(data.length==1)
			{
				context.arc(start_x + (0.5 * each_cel_width)-2,start_y + total_height-(data[i]-data_min)*height_gap-2, point_size, 0, 2 * Math.PI, false);
			}
			else{
			
				// context.arc(start_x + (i * each_cel_width), start_y + total_height - (total_height * (data[i] / data_max)), point_size, 0, 2 * Math.PI, false);
				context.arc(start_x + (i * each_cel_width)-2,  start_y + total_height-(data[i]-data_min)*height_gap-2, point_size, 0, 2 * Math.PI, false);							
			}
			context.fillStyle = 'red';
			context.fill();
			
			// context.lineWidth = point_size;
			// context.strokeStyle = 'red';
			// context.stroke();
		}
	}

	///////////////점 그리기 ///////////////////////

	/////////////////값 //////////////////
	for ( i = 0; i < data.length; i++) {

		if (data[i] == 0) {
			context.translate(0.5, 0.5);
			context.fillStyle = '#c0c0c0';
			context.textAlign = 'left';
			context.font = '11pt Calibri';
			context.textBaseline = "top";
			if(data.length==1){
				context.fillText(data[i] + eksdnl, start_x + (0.5 * each_cel_width) + 5, end_y - 20);
			}
			else{
				context.fillText(data[i] + eksdnl, start_x + (i * each_cel_width) + 5, end_y - 20);							
			}
			// context.lineWidth = point_size;
			// context.strokeStyle = 'red';
			// context.stroke();

		} else {

			// context.lineWidth = point_size;
			// context.strokeStyle = 'red';
			// context.stroke();
			context.translate(0.5, 0.5);
			context.fillStyle = '#c0c0c0';
			context.textAlign = 'left';
			context.font = '11pt Calibri';
			context.textBaseline = "top";
			if(data.length==1){
				//context.fillText(data[i] + eksdnl, start_x + (0.5 * each_cel_width) + 5, sstart_y + total_height-(data[i]-data_min)*height_gap - 20);
			}
			else{
				context.fillText(data[i] + eksdnl, start_x + (i * each_cel_width) + 5, start_y + total_height-(data[i]-data_min)*height_gap - 20);
																						
			}
		}
	}
	/////////////////값 //////////////////

}