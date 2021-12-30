/**
 * FUNCIONES GENERALES PARA EL USO EN EL LENGUAJE JAVASCRIPT
 * DESARROLLADAS POR Pacarina Media Lab (Pcrn)
 * 2021-10-05
 * 
 */

var Pcrn = new function()
{
    /**
     * Controlar el value de una variable numérica para que su value permanezca
     * en un rango determinado
     * 
     * @param {type} $value
     * @param {type} $min
     * @param {type} $max
     * @returns {unresolved}
     */
    this.limit_between = function($value, $min, $max)
    {
        $limited_value = $value;

        if ( $limited_value < $min ) $limited_value = $min;
        if ( $limited_value > $max ) $limited_value = $max;

        return $limited_value;
    };
    
    /**
     * Controlar el value de una variable numérica para que su value permanezca
     * en un rango determinado,
     * Si supera el máximo devuelve el mínimo
     * Si supera el mínimo devuelve el máximo
     * 
     * @param {type} $value
     * @param {type} $min
     * @param {type} $max
     * @returns {unresolved}
     */
    this.cycle_between = function($value, $min, $max)
    {
        $limited_value = $value;

        if ( $limited_value < $min ) $limited_value = $max;
        if ( $limited_value > $max ) $limited_value = $min;

        return $limited_value;
    };

    /**
     * Redondea un número con cierto número de decimales
     * 
     * @param {number} num 
     * @param {number} decimals 
     */
    this.round = function round(num, decimals = 2) {
        var sign = (num >= 0 ? 1 : -1);
        num = num * sign;
        if (decimals === 0) //con 0 decimals
            return sign * Math.round(num);
        // round(x * 10 ^ decimals)
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimals) : decimals)));
        // x * 10 ^ (-decimals)
        num = num.toString().split('e');
        return sign * (num[0] + 'e' + (num[1] ? (+num[1] - decimals) : -decimals));
    }

    /**
     * Redondea un número con cierto número de decimales
     * 
     * @param {number} num 
     * @param {number} total
     */
    this.intPercent = function intPercent(num, total = 100) {
        var intPercent = this.round(100 * num / total, 0)
        return intPercent
    }
};