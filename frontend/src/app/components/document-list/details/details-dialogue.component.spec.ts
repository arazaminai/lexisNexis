import { ComponentFixture, TestBed } from '@angular/core/testing';
import { DetailsDialogueComponent } from './details-dialogue.component';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';

describe('DetailsDialogueComponent', () => {
  let component: DetailsDialogueComponent;
  let fixture: ComponentFixture<DetailsDialogueComponent>;

  const mockData = {
    filename: 'test.pdf',
    filetype: 'application/pdf',
    uploaded_at: '2023-01-01T00:00:00Z',
    highlight: '<b>highlighted</b>',
    host: 'http://localhost',
    filepath: '/files/test.pdf'
  };

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DetailsDialogueComponent],
      providers: [
        { provide: MAT_DIALOG_DATA, useValue: mockData }
      ]
    }).compileComponents();

    fixture = TestBed.createComponent(DetailsDialogueComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should receive data', () => {
    expect(component.data.filename).toBe('test.pdf');
    expect(component.data.filetype).toBe('application/pdf');
  });
});