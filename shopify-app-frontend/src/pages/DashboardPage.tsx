import React from 'react';
import {Card, Text, Box, Button, Layout, Frame, Toast} from '@shopify/polaris';
import api from '../api/client';

const DashboardPage: React.FC = () => {
  const [summary, setSummary] = React.useState<any>(null);
  const [toast, setToast] = React.useState<{content: string} | null>(null);

  const load = async () => {
    const {data} = await api.get('/summary');
    setSummary(data);
  };
  React.useEffect(() => { load(); }, []);

  const syncProducts = async () => {
    await api.post('/sync/products');
    setToast({content: 'Products sync queued'});
    setTimeout(load, 1500);
  };

  return (
    <Frame>
      {toast && <Toast content={toast.content} onDismiss={() => setToast(null)} />}
      <Layout>
        <Layout.Section>
          <Card roundedAbove="sm">
            <Box padding="400">
              <Text as="h2" variant="headingMd">Overview</Text>
              <Box paddingBlockStart="200">
                <Text as="p">Total Products: {summary?.products ?? 0}</Text>
                <Text as="p">Collections: {summary?.collections ?? 0}</Text>
                <Text as="p">Last Sync: {summary?.lastSyncTime ?? '—'}</Text>
              </Box>
            </Box>
          </Card>
        </Layout.Section>
        <Layout.Section>
          <Card roundedAbove="sm">
            <Box padding="400">
              <Text as="h3" variant="headingSm">Actions</Text>
              <Box paddingBlockStart="200">
                {/* Polaris v12: use variant="primary" */}
                <Button variant="primary" onClick={syncProducts}>Sync Products</Button>
              </Box>
            </Box>
          </Card>
        </Layout.Section>
      </Layout>
    </Frame>
  );
};

export default DashboardPage;
