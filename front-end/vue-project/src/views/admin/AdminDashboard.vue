<script setup>
import { ref } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

// --- DỮ LIỆU BIỂU ĐỒ ---

// 1. Biểu đồ Tròn: Tỉ lệ trạng thái Nhóm
const groupChartOptions = ref({
  chart: {
    type: 'donut',
    fontFamily: 'Segoe UI, sans-serif',
  },
  labels: ['Đã đủ thành viên', 'Chưa đủ thành viên'],
  colors: ['#A855F7', '#EAB308'], // Màu Tím (Đủ) và Vàng (Thiếu) khớp với Card
  legend: {
    position: 'bottom',
  },
  dataLabels: {
    enabled: true,
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Tổng nhóm',
            fontSize: '16px',
            fontWeight: 600,
            color: '#373d3f',
          },
        },
      },
    },
  },
})
const groupChartSeries = ref([86, 12]) // Số liệu từ Card

// 2. Biểu đồ Cột: Thống kê tổng quan
const overviewChartOptions = ref({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    fontFamily: 'Segoe UI, sans-serif',
  },
  plotOptions: {
    bar: {
      borderRadius: 4,
      columnWidth: '45%',
      distributed: true, // Để mỗi cột 1 màu khác nhau
    },
  },
  dataLabels: {
    enabled: false,
  },
  xaxis: {
    categories: ['Người dùng', 'Lớp học', 'Tổng nhóm'],
    labels: {
      style: { fontSize: '12px' },
    },
  },
  colors: ['#3B82F6', '#22C55E', '#F97316'], // Xanh dương, Xanh lá, Cam
  legend: { show: false }, // Ẩn chú thích vì màu đã rõ theo cột
})
const overviewChartSeries = ref([
  {
    name: 'Số lượng',
    data: [1240, 45, 98], // 98 là tổng (12 + 86)
  },
])
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tổng quan hệ thống</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div
        class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Tổng số người dùng</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">1,240</p>
          </div>
          <div class="p-3 bg-blue-100 rounded-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-blue-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
          </div>
        </div>
      </div>

      <div
        class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Lớp học phần</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">45</p>
          </div>
          <div class="p-3 bg-green-100 rounded-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-green-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
              />
            </svg>
          </div>
        </div>
      </div>

      <div
        class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Nhóm chưa đủ</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">12</p>
          </div>
          <div class="p-3 bg-yellow-100 rounded-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-yellow-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
              />
            </svg>
          </div>
        </div>
      </div>

      <div
        class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-500 text-sm font-medium">Nhóm đã đủ</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">86</p>
          </div>
          <div class="p-3 bg-purple-100 rounded-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-purple-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Quy mô hoạt động hệ thống</h3>
        <VueApexCharts
          type="bar"
          height="350"
          :options="overviewChartOptions"
          :series="overviewChartSeries"
        />
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold text-gray-700 mb-4">Tỉ lệ hoàn thành nhóm</h3>
        <div class="flex items-center justify-center h-full pb-6">
          <VueApexCharts
            type="donut"
            width="380"
            :options="groupChartOptions"
            :series="groupChartSeries"
          />
        </div>
      </div>
    </div>
  </div>
</template>
