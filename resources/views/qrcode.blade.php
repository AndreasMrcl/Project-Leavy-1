<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Users</title>
    @include('layout.head')
    <link
      href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css"
      rel="stylesheet" />
  </head>
  
  <body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->
    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
      <!-- Navbar -->
      @include('layout.navbar')
      <!-- end Navbar -->
      <div class="p-5">
        <div class="w-full rounded-xl bg-white h-fit mx-auto">
          <div class="p-8">
            <div class="space-y-8">
              <div class="mx-auto text-center">
                <h1 class="text-3xl">QR Code for {{ $user->name }}</h1>
              </div>
              <div class="mx-auto text-center">
                <img class="text-center mx-auto" src="{{ asset('storage/' . $filename) }}" alt="QR Code" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
