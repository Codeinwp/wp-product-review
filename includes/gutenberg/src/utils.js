export const reverseObject = ( object ) => {
	const newObject = {};
	const keys = [];
	for ( let key in object ) {
		keys.push( key );
	}
	for ( let i = keys.length - 1; i >= 0; i-- ) {
		const value = object[ keys[i] ];
		newObject[ keys[i] ] = value;
	}
	return newObject;
}