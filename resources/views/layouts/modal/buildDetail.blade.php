<div class="build-details p-md-4">
    <table class="table table-striped table-condensed">
        <tbody>
        <tr>
            <td>Build Name</td>
            <td>{{$buildName ?? __('Unknown')}}</td>
        </tr>
        <tr>
            <td>Version</td>
            <td>{{$version ?? __('Unknown')}}</td>
        </tr>
        <tr>
            <td>Product</td>
            <td>{{$productName ?? __('Unknown')}} ({{$product ?? __('Unknown')}})</td>
        </tr>
        <tr>
            <td>Detected at</td>
            <td>{{$detected ?? __('Unknown')}}</td>
        </tr>
        </tbody>
    </table>
    <h4 class="modal-title text-black fw-light mt-md-4">Configs</h4>
    <table class="table table-sm table-striped table-condensed">
        <thead>
        <tr>
            <th>File</th>
            <th>CDN hash</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><b>Build Config</b></td>
            <td><span class="badge bg-{{empty($buildConfig) ? 'warning' : 'success'}} hash">{{$buildConfig ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>CDN Config</b></td>
            <td><span class="badge bg-{{empty($cdnConfig) ? 'warning' : 'success'}} hash">{{$cdnConfig ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>Patch Config</b></td>
            <td>
                @if (!empty($patchConfig))
                <span class="badge bg-success hash">{{$patchConfig}}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><b>Product Config</b></td>
            <td><span class="badge bg-{{empty($productConfig) ? 'warning' : 'success'}} hash">{{$productConfig ?? __('Unknown')}}</span></td>
        </tr>
        </tbody>
    </table>
    <h4 class="modal-title text-black fw-light mt-md-4">System files</h4>
    <table class="table table-sm table-striped table-condensed">
        <thead>
        <tr>
            <th>File</th>
            <th>Content hash</th>
            <th>Encoding/CDN hash</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><b>Encoding</b></td>
            <td><span class="badge bg-secondary hash">{{$encodingContentHash ?? __('Unknown')}}</span></td>
            <td><span class="badge bg-success hash">{{$encodingCdnHash ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>Root</b></td>
            <td><span class="badge bg-secondary hash">{{$rootContentHash ?? __('Unknown')}}</span></td>
            <td><span class="badge bg-success hash">{{$rootCdnHash ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>Install</b></td>
            <td><span class="badge bg-secondary hash">{{$installContentHash ?? __('Unknown')}}</span></td>
            <td><span class="badge bg-success hash">{{$installCdnHash ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>Download</b></td>
            <td><span class="badge bg-secondary hash">{{$downloadContentHash ?? __('Unknown')}}</span></td>
            <td><span class="badge bg-success hash">{{$downloadCdnHash ?? __('Unknown')}}</span></td>
        </tr>
        <tr>
            <td><b>Size</b></td>
            <td><b class="badge bg-secondary hash">{{$sizeContentHash ?? __('Unknown')}}</b></td>
            <td><b class="badge bg-success hash">{{$sizeCdnHash ?? __('Unknown')}}</b></td>
        </tr>
        </tbody>
    </table>
</div>
