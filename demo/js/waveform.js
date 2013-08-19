function drawWaveform(canvas, waveform_data, scale)
{
  if(canvas.getContext)
  {
    var colors = ['#036', '#069', '#09c'];

    var ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ddd'
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    if (!scale) scale = 1;

    for(var color_i = 0; color_i < colors.length; color_i++)
    {
      for(var offset = 0; offset < 2; offset++)
      {
        ctx.beginPath();
          ctx.moveTo(0, canvas.height / 2);
          for(var i = offset; i < waveform_data.length; i += 2)
          {
            ctx.fillStyle = colors[color_i];
            var x = i / waveform_data.length * canvas.width;
            var y = waveform_data[i] * scale * canvas.height / 2 * ((colors.length - color_i) / colors.length) +
              canvas.height / 2;
            ctx.lineTo(x, y);
          }
          ctx.lineTo(canvas.width, canvas.height / 2);
          ctx.fill();
        ctx.closePath();
      }
    }

    ctx.beginPath();
      ctx.lineWidth = 1;
      ctx.strokeStyle = '#fff';
      ctx.moveTo(0, canvas.height / 2);
      ctx.lineTo(canvas.width, canvas.height / 2);
      ctx.stroke();
    ctx.closePath();
  }
  else
    alert("canvas not supported");
}
