/**
 * This is tyrax error handler set-up
 * this catches http error from tarax request
 * @param {*} err 
 * @param {*} message 
 */

export default function errorHandler(err, message){
    /**
     * Default is alert(), you can change it.
     */
    alert(message);

}