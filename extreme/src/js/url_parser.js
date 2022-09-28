function validateURL(url) {
    return url.startsWith('https://www.google.com/maps/dir/')
}

function parseNameFromURL(url) {
    let arr = url.replace('https://www.google.com/maps/dir/', '').split('/')
    return {
        startPointName: getDestName(arr[0]),
        endPointName: getDestName(arr[1])
    }
}

function getDestName(str) {
    let target = decodeURIComponent(str)
    if (!target.match(/、|\+/)) return target;
    if (target.match('、')) {
        return target.split('、')[0]
    } else {
        let arr = target.split('+')
        arr.shift()
        return arr.join(' ')
    }
}