import { major, minor } from 'semver';

export default {
    async load() {
        return await fetch('https://packagist.org/p2/kreyu/data-table-bundle.json')
            .then(response => response.json())
            .then(body => body.packages['kreyu/data-table-bundle'].shift().version)
            .then(version => major(version).toString() + '.' + minor(version).toString() + '.*')
        ;
    }
}
