/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'export', // 👈 ensures Next.js generates static HTML
  basePath: '/your-repo-name', // 👈 replace with your GitHub repo name
  images: {
    unoptimized: true, // 👈 disables image optimization (not supported on GH Pages)
  },
};

export default nextConfig;
