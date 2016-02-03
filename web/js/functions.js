/**
 * Created by Kraft on 28.01.2016.
 */
function date2str(x, y) {
	var z = {
		M: x.getMonth() + 1,
		d: x.getDate(),
		h: x.getHours(),
		m: x.getMinutes(),
		s: x.getSeconds()
	};
	y = y.replace(/(M+|d+|h+|m+|s+)/g, function(v) {
		return ((v.length > 1 ? "0" : "") + eval('z.' + v.slice(-1))).slice(-2)
	});

	return y.replace(/(y+)/g, function(v) {
		return x.getFullYear().toString().slice(-v.length)
	});
}