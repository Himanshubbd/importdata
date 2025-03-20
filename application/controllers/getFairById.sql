CREATE   procedure [dbo].[getFairById] --95266  
@FairId int,
@AccelEventID int = null
as  
begin  
declare
if(@AccelEventID IS NOT NULL)
begin
SELECT top 1 @FairId = UID FROM tblFairList where  AccelEventID = @AccelEventID;
end
 @IsNewEvent bit=(select NewStyle from tblFairList where UID=@FairId)  
select try_cast(isnull(@IsNewEvent,0) as bit) as IsNewEvent  
select *,OnlineFairLink,  
(select count(distinct UniversityId) from tblFairUniversities where FairId=UID) As NumberRegistration,  
isnull(StaffNotificationProfile,(select FairStaffProfileId from tbloffices where id=(select office from tblfairlist where uid=@FairId))) As StaffProfile,  
isnull(StudentNotificationProfile,(select FairStudentProfileId from tbloffices where id=(select office from tblfairlist where uid=@FairId))) As StudentProfile  
from tblFairList fl where UID=@FairId  
if(@IsNewEvent=0)  
begin  
  
select FP.Id,FP.FairId,FP.PackageId,FP.CreationDate,ISnull(FP.Price,0) As Price,FP.Description,FP.Active,FP.Name,FP.BaseProductId,FP.ProductRegionId,tblPackages.Name from tblFairPackages FP  
join tblPackages on tblPackages.Id=FP.PackageId  
 where FairId=@FairId  
  
  
select Id as Id,Name  from tblPackages   
select FA.ID, FA.AddonId,FA.AddonName,FA.FairCategoryId,FA.DisplayName,isnull(FA.Price,0) as Price ,FA.FairCategoryId ,FA.GroupName,FA.Active,FA.ProductDescription From  tblFairAddons FA  
--left join tblAddonsCategies on tblAddonsCategies.Id=tblFairAddons.AddonsCategiesId  
 where FA.AddonId=@FairId  
  
select * from EventAttendanceType  
  
  
end  
else  
begin  
select FP.Id,FP.FairId,FP.PackageId,FP.CreationDate,ISnull(FP.Price,0) As Price,FP.Description,FP.Active,FP.Name,FP.BaseProductId,FP.ProductRegionId,tblPackages.Name  from tblFairPackages FP  
join tblPackages on tblPackages.Id=FP.PackageId  
 where FairId=@FairId  
 and Active=1  
  
select Id as Id,Name  from tblPackages   
  
select fp.Id as Id,fp.Name Name from tblFairCategory fp  
  
select FA.ID,FA.AddonId,FA.AddonName,Isnull(FA.Price,0) as Price,Isnull(FA.Price2,0) as Price2,FA.ProductId,FA.Active,FA.crtDate,FA.AddonsCategiesId,FA.FairCategoryId,FA.GroupName,FA.ProductRegionId,FA.BaseProductId,FA.DisplayName,FA.ProductDescription fr
om tblFairAddons FA  
--left join tblAddonsCategies on tblAddonsCategies.Id=tblFairAddons.AddonsCategiesId  
 where AddonId=@FairId and Active=1  
 union  
select Id as ID, FairId as AddonId,Name as AddonName,Isnull(Price,0) as Price,Isnull(Price,0) as Price2,null as ProductId,Active as Active,CreationDate as crtDate,null as AddonsCategiesId,FairCategoryId as FairCategoryId,GroupName as GroupName,ProductRegi
onId as ProductRegionId,BaseProductId as BaseProductId,DisplayName,ProductDescription    
from tblFairPackages  
where FairId=@FairId and FairCategoryId=1 and Active=1  
  
 select * from EventAttendanceType  
  
  
end  
end  