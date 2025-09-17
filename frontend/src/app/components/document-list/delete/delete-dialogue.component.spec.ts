import { ComponentFixture, TestBed } from '@angular/core/testing';
import { DeleteDialog } from './delete-dialogue.component';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

describe('DeleteDialog', () => {
  let component: DeleteDialog;
  let fixture: ComponentFixture<DeleteDialog>;
  let dialogRefSpy: jasmine.SpyObj<MatDialogRef<DeleteDialog>>;

  const mockData = { filename: 'test.pdf' };

  beforeEach(async () => {
    dialogRefSpy = jasmine.createSpyObj('MatDialogRef', ['close']);

    await TestBed.configureTestingModule({
      imports: [DeleteDialog],
      providers: [
        { provide: MatDialogRef, useValue: dialogRefSpy },
        { provide: MAT_DIALOG_DATA, useValue: mockData }
      ]
    }).compileComponents();

    fixture = TestBed.createComponent(DeleteDialog);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should receive filename in data', () => {
    expect(component.data.filename).toBe('test.pdf');
  });

  it('should close dialog with false when cancel is clicked', () => {
    component.dialogRef.close(false);
    expect(dialogRefSpy.close).toHaveBeenCalledWith(false);
  });

  it('should close dialog with true when delete is clicked', () => {
    component.dialogRef.close(true);
    expect(dialogRefSpy.close).toHaveBeenCalledWith(true);
  });
});