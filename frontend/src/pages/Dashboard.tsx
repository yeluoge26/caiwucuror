import { useEffect, useState } from 'react'
import { Card, Row, Col, Statistic, Spin } from 'antd'
import { DollarOutlined, ArrowUpOutlined, ArrowDownOutlined } from '@ant-design/icons'
import apiClient from '../api/client'

interface DashboardData {
  today: {
    income: number
    expense: number
    net: number
  }
  month: {
    income: number
    expense: number
    net: number
  }
}

export default function Dashboard() {
  const [loading, setLoading] = useState(true)
  const [data, setData] = useState<DashboardData | null>(null)

  useEffect(() => {
    loadData()
  }, [])

  const loadData = async () => {
    try {
      setLoading(true)
      const response = await apiClient.get('?r=reports/dashboard')
      if (response.success) {
        setData(response.data)
      }
    } catch (error) {
      console.error('Failed to load dashboard data:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', padding: 50 }}>
        <Spin size="large" />
      </div>
    )
  }

  return (
    <div>
      <h1 style={{ marginBottom: 24 }}>仪表盘</h1>
      <Row gutter={[16, 16]}>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="今日收入"
              value={data?.today.income || 0}
              prefix={<ArrowUpOutlined />}
              valueStyle={{ color: '#3f8600' }}
              suffix="VND"
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="今日支出"
              value={data?.today.expense || 0}
              prefix={<ArrowDownOutlined />}
              valueStyle={{ color: '#cf1322' }}
              suffix="VND"
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="今日净额"
              value={data?.today.net || 0}
              prefix={<DollarOutlined />}
              valueStyle={{ color: data?.today.net && data.today.net >= 0 ? '#3f8600' : '#cf1322' }}
              suffix="VND"
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="本月净额"
              value={data?.month.net || 0}
              prefix={<DollarOutlined />}
              valueStyle={{ color: data?.month.net && data.month.net >= 0 ? '#3f8600' : '#cf1322' }}
              suffix="VND"
            />
          </Card>
        </Col>
      </Row>
    </div>
  )
}
